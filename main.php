<?php 
ini_set('display_errors', '0'); //보안을 위해 

//GET으로 날짜의 정보 표시 형식 고르기
date_default_timezone_set('Asia/Seoul');
if (isset($_GET['date'])) {
    $date = DateTime::createFromFormat('Ymd', $_GET['date']);
    $outputDate = $date->format('Y년 m월 d일');
    $SearchDate = $_GET['date'];
    $message = $outputDate . " 급식";
} else {
    $SearchDate = date("Ymd");
    $message = date("Y", time()) . " / " . date("m", time()) . " / " . date("d", time());
}

// 캐싱 폴더 경로
$cacheFolder = 'DB';

// 날짜를 기준으로 파일명 생성
$fileName = $cacheFolder . '/' . $SearchDate . '.txt';

// 캐싱 파일 감지
if (file_exists($fileName)) {
    // 캐싱 읽어오기
    $cachedData = file_get_contents($fileName);
    $cachedData = json_decode($cachedData, true);

    // 캐싱된 정보 불러와서 배열에 넣기
    $lunch = $cachedData['lunch'];
    $dinner = $cachedData['dinner'];
} else {
    // API 요청 및 응답 받아오기
    $OpenAPIKEY = '나이스 API 키';
    $ATPT_OFCDC_SC_CODE = '시도교육청코드드';
    $SD_SCHUL_CODE = '행정표준코드';
    $apiUrl = 'https://open.neis.go.kr/hub/mealServiceDietInfo?ATPT_OFCDC_SC_CODE='.$ATPT_OFCDC_SC_CODE.'&SD_SCHUL_CODE='.$SD_SCHUL_CODE.'&KEY='.$OpenAPIKEY.'&MLSV_YMD=' . $SearchDate;
    $response = file_get_contents($apiUrl);

    // XML을 SimpleXMLElement로 파싱
    $xml = new SimpleXMLElement($response);

    // MMEAL_SC_NM 및 DDISH_NM 값을 추출해서 배열에 넣기
    $lunch = [
        'mmealScNm' => (string)$xml->row[0]->MMEAL_SC_NM,
        'ddishNm' => preg_replace("/\([^)]+\)/", "", (string)$xml->row[0]->DDISH_NM),
    ];

    $dinner = [
        'mmealScNm' => (string)$xml->row[1]->MMEAL_SC_NM,
        'ddishNm' => preg_replace("/\([^)]+\)/", "", (string)$xml->row[1]->DDISH_NM),
    ];

    // 파일 
    file_put_contents($fileName, json_encode(['lunch' => $lunch, 'dinner' => $dinner]));
}
?> 
<!DOCTYPE html> 
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>급식 정보</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 18px;
            text-align: center;
            background-color: #5eacd1;
            margin: 0;
            padding: 20px;
            padding-top: 140px;
            width: 510px;
            height: 1080px;
            overflow: hidden;
        }
        h1 {
            font-size: 30pt;
            color: #333;
        }
        #meal-boxes {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }
        .meal-box-head {
            font-weight: bold; 
            color: rgb(15, 161, 230); 
        }
        .meal-box {
            border: 2px solid #333;
            border-radius: 10px;
            padding: 20px;
            width: 350px;
            background-color: #fff;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }
        .food {
            margin-top: 10px;
            color: #555;
        }
        #quote-box {
            margin-top: 10px;
            font-style: Arial;
            font-size: 12pt;
            color: #000000;
            width: 660px;
        }
    </style>
</head>
<body>
    <h1 class="date"><?php echo $message; ?></h1>
    <div id="meal-boxes" class="meal-box-head">
        <div class="meal-box">중식
            <div class="food"><?php echo $lunch['ddishNm'] ?: '급식 정보가 없습니다.'; ?></div>
        </div>
        <div class="meal-box">석식
            <div class="food"><?php echo $dinner['ddishNm'] ?: '급식 정보가 없습니다.'; ?></div>
        </div>
    </div>
    <div id="quote-box">
        <div class="message2say">울산고등학교 학생회 봉사부 제공</div>
    </div>
</body>
</html>
