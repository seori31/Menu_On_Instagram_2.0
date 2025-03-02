from selenium import webdriver
from PIL import Image
import time 
from instagrapi import Client 
import datetime 

class insta: 
    def web_screenshot(self): 

        # 웹 드라이버 설정 
        driver = webdriver.Chrome()
        driver.set_window_size(566,1080)

        # 웹 페이지 로드
        url = 'http://localhost/main.php'  # 호스팅 URL
        driver.get(url)

        # 페이지가 완전히 로드되도록 잠시 대기
        time.sleep(3)

        # 스크린샷 저장 (스크린샷은 PNG 형식으로 저장됨)
        driver.save_screenshot('screenshot.png')

        # PNG 파일을 JPG로 변환
        image = Image.open('screenshot.png')
        image.convert('RGB').save('screenshot.jpg', 'JPEG')

        # 웹 드라이버 종료
        driver.quit()

    def login_upload(self): 
        cl = Client()
        cl.delay_range = [1,3]
        cl.load_settings("session.json") 
        cl.delay_range = [1,3]
        cl.login('USERNAME', 'PASSWORD')  
        cl.delay_range = [1,3]
        cl.get_timeline_feed() 
        cl.delay_range = [1,3]

        cl.photo_upload_to_story('current/path/screenshot.jpg') 
        cl.delay_range = [1,3] 

    def timecheck(self): 
        print("Task completed at:", datetime.datetime.now()) 
        
run = insta() 
run.web_screenshot() 
run.login_upload() 
run.timecheck() 