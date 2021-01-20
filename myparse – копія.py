from typing import Any, List, Dict
from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
import time
import csv
import requests
import os
import shutil
import re

def get_html(url: str) -> webdriver:
    try:
        driver = webdriver.Chrome(ChromeDriverManager().install())
        driver.maximize_window()
        driver.get(url)
        return driver
    except Exception as e:
        print('Can\'t get Site HTML. Reason %s.' % e)

def get_img(driver: webdriver) -> List[str]:
    try:
        driver.implicitly_wait(5)
        imgs: List[str] = driver.find_elements_by_css_selector('div.smallImg>ul>li>a>img')
        src: List[str] = []
        for img in imgs:
            src.append(img.get_attribute('src').replace('_S', '_L'))
        return src
    except Exception as e:
        print('Can\'t get Images SRC List. Reason %s.' % e)

def get_img_str(driver: webdriver) -> str:
    try:
        driver.implicitly_wait(5)
        imgs: List[str] = driver.find_elements_by_css_selector('div.smallImg>ul>li>a>img')
        img_str: str = ''
        for img in imgs:
            src_str = img.get_attribute('src')
            str_arr = str(src_str).split('/')
            str_arr.reverse()
            img_str += str_arr[0].replace('_S', '_L')+'[:param:][alt='+driver.find_element_by_css_selector('h1.vehicle-Tit').text+'][title='+driver.find_element_by_css_selector('h1.vehicle-Tit').text+']|'
        return img_str[:-1]
    except Exception as e:
        print('Can\'t get Images str. Reason %s' % e)

def get_category(driver: webdriver, i: int) -> str:
    try:
        wait = WebDriverWait(driver, 5)
        elements = wait.until(EC.visibility_of_element_located((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[3]')))
        return elements.text
    except Exception as e:
        print('Can\'t get Category Text. Reason %s.' % e)

def get_model(driver: webdriver, i: int) -> str:
    try:
        wait = WebDriverWait(driver, 5)
        element = wait.until(EC.visibility_of_element_located((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[4]')))
        return element.text
    except Exception as e:
        print('Can\'t get Model Text. Reason %s.' % e)

def get_type(driver: webdriver, i: int) -> str:
    try:
        wait = WebDriverWait(driver, 5)
        element = wait.until(EC.visibility_of_element_located((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[5]')))
        return element.text
    except Exception as e:
        print('Can\'t get Type Text. Reason %s.' % e)

def get_year(driver: webdriver, i: int) -> str:
    try:
        wait = WebDriverWait(driver, 5)
        element = wait.until(EC.visibility_of_element_located((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[6]')))
        return element.text
    except Exception as e:
        print('Failed to get year. Reason: %s' % e)

def get_price(driver: webdriver, i: int) -> str:
    try:
        wait = WebDriverWait(driver, 5)
        element = wait.until(EC.visibility_of_element_located((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[11]')))
        return element.text
    except Exception as e:
        print('Failed to get price. Reason: %s' % e)

def save_csv(items: List[str], path: str) -> None:
    try:
        with open(path, 'w', newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow(['Категория', 'URL категории', 'Товар', 'Вариант', 'Описание', 'Цена', 'URL', 'Изображение', 'Артикул', 'Количество', 'Активность', 'Заголовок [SEO]', 'Ключевые слова [SEO]', 'Описание [SEO]', 'Старая цена', 'Рекомендуемый', 'Новый', 'Сортировка', 'Вес', 'Связанные артикулы', 'Смежные категории', 'Ссылка на товар', 'Валюта', 'Свойства'])
            for item in items:
                writer.writerow([item['category'], item['url_category'], item['title'], ' ', item['description'], item['price'], item['url'], item['images'], ' ', item['count'], item['activation'], ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', item['currency'], ' '])
    except Exception as e:
        print('Failed to save CSV. Reason %s.' % e)

def download_images(img_urls: List[str]) -> None:
    try:
        for img_url in img_urls:
            str_arr = str(img_url).split('/')
            str_arr.reverse()
            img_data = requests.get(img_url).content
            with open('./py_img/' + str_arr[0], 'wb') as handler:
                handler.write(img_data)
    except Exception as e:
        print('Failed download image. Reason: %s' % e)

def click_next_page(driver: webdriver, cars_list = []) -> None:
    try:
        for nav in range(2, 3):
            #time.sleep(10)
            if(nav != 2):
                wait = WebDriverWait(driver, 5)
                element = wait.until(EC.element_to_be_clickable((By.XPATH, '//*[@id="pagingBar_P"]/ul/li['+str(nav)+']/a')))
                element.click()
                time.sleep(10)
                print('Page '+str(nav))
            for i in range(1, 11):
                print('Car index '+str(i))
                car = get_car(driver, i)
                car['title'] = get_category(driver, i) + get_model(driver, i)
                car['category'] = get_category(driver, i)
                car['url_category'] = re.sub('\s+', '-', car['category']).lower()
                car['price'] = re.sub('US\$', '', get_price(driver, i))
                car['url'] = re.sub('[\[\]\(\)\~]+', '', car['title'])
                car['url'] = re.sub('\s+', '-', car['url']).lower()
                car['url'] = car['url'][1:]
                cars_list.append(car)
                print(car['title'])
            wait = WebDriverWait(driver, 5)
            element = wait.until(EC.element_to_be_clickable((By.XPATH, '//*[@id="header"]/ul[1]/li[4]/a')))
            element.click()
            time.sleep(10)
        save_csv(cars_list, 'test.csv')
    except Exception as e:
        print('Failed to Click Page Navigator. Reason: %s' % e)

def clear_folder(folder: str) -> None:
    for filename in os.listdir(folder):
        file_path = os.path.join(folder, filename)
        try:
            if os.path.isfile(file_path) or os.path.islink(file_path):
                os.unlink(file_path)
            elif os.path.isdir(file_path):
                shutil.rmtree(file_path)
        except Exception as e:
            print('Failed to delete %s. Reason: %s' % (file_path, e))

def rm_new_line(string):
    pattern = re.compile(r'[\n+\t+\r+]')
    return re.sub(pattern, '', string)

def get_car(driver: webdriver, i: int) -> None:
    try:
        #time.sleep(10)
        wait: WebDriverWait = WebDriverWait(driver, 10)
        element = wait.until(EC.element_to_be_clickable((By.XPATH, '//*[@id="tbl_list_p"]/tbody/tr[' + str(i) + ']/td[3]/a')))
        element.click()
        tabs = driver.window_handles
        driver.switch_to.window(tabs[1])
        car: Dict[str] = {}
        car['description'] = driver.find_element_by_css_selector('div.search-v>table.tableV').get_attribute('outerHTML')
        car['description'] += driver.find_element_by_xpath('//*[@id="container"]/div[2]/div[3]/h2[2]').get_attribute('outerHTML')
        car['description'] += driver.find_element_by_xpath('//*[@id="container"]/div[2]/div[3]/table[2]').get_attribute('outerHTML')
        #car['description'] += driver.find_element_by_xpath('//*[@id="container"]/div[2]/div[3]/h2[3]').get_attribute('outerHTML').rstrip("\n")
        #car['description'] += driver.find_element_by_xpath('//*[@id="container"]/div[2]/div[3]/div[2]').get_attribute('outerHTML').rstrip("\n")
        car['description'] = rm_new_line(car['description']).encode("utf-8")
        car['images'] = get_img_str(driver)
        car['count'] = '-1'
        car['activation'] = '1'
        car['currency'] = 'USD'
        download_images(get_img(driver))
        driver.close()
        driver.switch_to.window(tabs[0])
        return car
    except Exception as e:
        print('Can\'t find element. Reason: %s' % e)

def create_folder(path):
    try:
        if not os.path.exists(path):
            os.makedirs(path)
    except Exception as e:
        print('Can\'t create folder in path %s. Reason %s' % path, e)

create_folder('./py_img')
clear_folder('./py_img/')
driver = get_html('http://ajitkorea.com/Auction/auction-L.jsp')
click_next_page(driver)
driver.quit()

