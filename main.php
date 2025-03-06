<?php 
ini_set('display_errors', '0'); //보안을 위해 

//GET으로 날짜의 정보 표시 형식 고르기 
date_default_time-zone_set('Asia/Seoul');
if (isset($_GET['date'])) {
    $date = DateTime::createFromFormat('Ymd', $_GET['date']);
    $outputDate = $date->format('Y년 m월 d일');
    $SearchDate = $_GET['date'];
    $message = $outputDate . " 급식";
} else {
    $SearchDate = date("Ymd");
    $message = "오늘은 " . date("m", time()) . "월 " . date("d", time()) . "일 입니다";
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
    $OpenAPIKEY = 'b2c27578723a428080d923b3d010a0c7';
    $apiUrl = 'https://open.neis.go.kr/hub/mealServiceDietInfo?ATPT_OFCDC_SC_CODE=H10&SD_SCHUL_CODE=7480035&KEY='.$OpenAPIKEY.'&MLSV_YMD=' . $SearchDate;
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
            text-align: center;
            background-color: #5eacd1;
            margin: 0;
            padding: 20px;
            padding-top: 140px;
            width: 500px;
            height: 1080px;
            overflow: hidden;
        }
        h1 {
            color: #333;
        }
        #meal-boxes {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
        }
        .meal-box {
            border: 2px solid #333;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            background-color: #fff;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }
        .food {
            margin-top: 10px;
            color: #555;
        }
        #quote-box {
            margin-top: 40px;
            font-style: italic;
            color: #777;
        }
    </style>
</head>
<body>
    <h1 class="date"><?php echo $message; ?></h1>
    <div id="meal-boxes">
        <div class="meal-box">중식
            <div class="food"><?php echo $lunch['ddishNm'] ?: '오늘은 급식이 제공되지 않습니다.'; ?></div>
        </div>
        <div class="meal-box">석식
            <div class="food"><?php echo $dinner['ddishNm'] ?: '데이터 누락 - 안내문을 확인해주세요.'; ?></div>
        </div>
    </div>
    <div id="quote-box">
        <div class="message2say">"오늘도 화이팅!"</div>
    </div>
</body>
</html>
