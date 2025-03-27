from selenium import webdriver
import time 
from instagrapi import Client 
import datetime 

class insta: 
    def web_screenshot(self): 
        driver = webdriver.Firefox() 
        driver.set_window_size(566,1080) 
        
        url = 'http://IPADDRESS/main.php'   # 호스팅 URL
        driver.get(url) 
        time.sleep(5) 
        
        driver.save_screenshot('screenshot.png') 
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

        cl.photo_upload_to_story('REPOSITORY/PATH/screenshot.png') 
        cl.delay_range = [1,3] 

    def timecheck(self): 
        print("Task completed at:", datetime.datetime.now()) 
        
run = insta() 
run.web_screenshot() 
# run.login_upload() 
run.timecheck() 