from typing import Any, List, Dict
from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.action_chains import ActionChains
from deep_translator import GoogleTranslator
from multiprocessing import Pool
from bs4 import BeautifulSoup
from random import choice
from random import uniform
import time
import csv
import requests
import os
import shutil
import re
import fake_useragent
from requests.auth import HTTPBasicAuth
import configparser

config = configparser.ConfigParser(allow_no_value=True)
config.read("settings.ini") 
user = fake_useragent.UserAgent().random
session = requests.Session()
#session.headers.update({'Authorization': 'Basic MTUyMDAwOjQyNzU=', 'Cookie': 'JSESSIONID=3aW0qmfaD1tXJaR9lQ8KLzASOEKKnpwo9TyAj1u6pUbCqaX1Jtw7pvO5vHCaKB1k.UlBBQV9kb21haW4vUlBBQV9IUEcyMQ==; _xm_webid_1_=734120936', 'User-Agent': user, 'Accept': '*/*', 'Accept-Encoding': 'gzip, deflate, br', 'Connection': 'keep-alive', 'Content-Length': '0'})

headers = {'Authorization':'Basic MTUyMDAwOjQyNzU=', 'user-agent': user, 'accept': '*/*'}
                                
#translate function 
def ko_translate(text, lan):
    try:
        res = GoogleTranslator(source='ko', target=lan).translate(text)
    except Exception as e:
        print('Failed to Translate text. Reason: %s' % e)
        res = ''
    return res
#read file function 
def read_file(file_name, delimiter):
    try:
        return open(file_name).read().split(delimiter)
    except Exception as e:
        print('Can\'t read %s. Reason %s.' % (file_name, e))
#parsing from proxy
proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) }
#session.proxies.update(proxy)

#удаляємо всі зайві пробіли, переноси строк і табуляції
def rm_new_line(string):
        try:
            string = re.sub("[\n\t\r]*", "", string)
            string = re.sub("<!-+(.|\s|\n)*?-+>", "", string)
            string = re.sub("\s{2,}", "", string) 
            string = re.sub("<\s+", "<", string) 
            string = re.sub("\s+>", ">", string) 
            string = re.sub("<\s+\/\s*", "</", string) 
            string = re.sub("&amp;", "&", string) 
            string = re.sub("&quot;", "\"", string) 
            string = re.sub("&apos;", "'", string) 
            string = re.sub("&gt;", "<", string) 
            string = re.sub("&lt;", ">", string) 
            return string
        except Exception as e:
                print('Failed to delete White Space Tabs and New Line. Reason: %s' % e)
#следит чтоб небило длины текста более 5000 символов
def text_len(text, lang):
    new_text = ''
    #text = str(list(text)[2])
    if(len(text)>1):
        if(len(text)>5000):
            i = 0
            while (i <= int(len(text)/4999)):
                if(i == int(len(text)/4999)):
                    new_text += rm_new_line(ko_translate(text[i*4999:len(text)], lang))
                else:
                    new_text += rm_new_line(ko_translate(text[i*4999:i+1*4999], lang))
                i+=1
        else: 
            new_text += rm_new_line(ko_translate(text, lang))
        return new_text
#витягуємо IP нашого парсера
def get_ip(html):
    try:
        soup = BeautifulSoup(html, 'html.parser')
        ip = soup.find('span', class_ = 'ip').text.strip()
        ua = soup.find('span', class_ = 'ip').find_next_sibling('span').text.strip()
        return (ip,ua)
    except Exception as e:
        print('Can\'t get MY IP. Reason %s.' % e)
#витягуємо html без сесії
def get_html(url, useragent=None, proxy=None):
    try:
        r = requests.get(url, headers=useragent, proxies=proxy)
        if r.status_code == 200:
            return r.text
        else: return r.status_code
    except Exception as e:
        print('Can\'t get HTML. Reason %s.' % e)
#залогінюємось на сайті
def login(login_link):
    try:
        session.headers.update({'Authorization':'Basic MTUyMDAwOjQyNzU=', 'user-agent': user, 'Accept': '*/*', 'Host': 'www.lotteautoauction.net', 'Content-Length': '0', 'Connection': 'keep-alive'})
        session.post(login_link)
        session_cookies = session.cookies
        return session_cookies.get_dict()
    except Exception as e:
        print('Can\'t get HTML form %s. Reason %s.' % (login_link, e))
#витягуємо html із сесію
def get_shtml(link):
    try:
        # res = re.findall("{'JSESSIONID': '([\d\w=.]+)', '_xm_webid_1_': '([\d]+)'}", str(login(login_link)))
        # cookie = '_xm_webid_1_={}; _ga=GA1.2.2027634233.1611498833; _gid=GA1.2.1777522609.1612255912; hpAuctSaveid=152000; JSESSIONID={}; _gat_gtag_UA_118654321_1='.format(res[0][1], res[0][0])
        cookie = '_xm_webid_1_={}; _ga=GA1.2.2027634233.1611498833; _gid=GA1.2.1777522609.1612255912; hpAuctSaveid=152000; JSESSIONID={}'.format(config['DEFAULT']['WEB_ID'], config['DEFAULT']['SESSION_ID'])
        session.headers.update({'Authorization':'Basic MTUyMDAwOjQyNzU=', 'user-agent': user, 'Cookie': cookie})  
        html = session.get(link)
        if html.status_code == 200:
            return html.text
        else: return html.status_code
    except Exception as e:
        print('Can\'t get HTML with session. Reason %s.' % e)
#пробег
def get_distance_driven(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.findall("[0-9]+", rm_new_line(str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(1) > td:nth-child(4)"))))
        return "".join(res)
    except Exception as e:
        print('Can\'t get driven distance. Reason %s.' % e)
        return ""
#топливо
def get_fuel(html):
    fulel_data = {
        "가솔린":"Бензин",
        "휘발유":"Бензин",
        "경유":"Бензин",
        "디젤":"Дизель",
        "LPG":"LPG",
        "하이브리드":"Гибрид",
        "LPI하이브리드":"LPG гибрид",
        "가솔린하이브리드":"Бензиновый гибрид",
        "디젤하이브리드": "Дизельный гибрид",
        "전기":"Электрокар",
        "가솔린/LPG":"Бензин/LPG"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return fulel_data[rm_new_line(str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(4) > td:nth-child(4)")[0].text.strip()))]
    except Exception as e:
        print('Can\'t get fuel. Reason %s.' % e)
        return ""
#марка авто
def get_car_mark(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:     
        return text_len(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > div > div > table > tbody > tr:nth-child(4) > td:nth-child(5)")[0].text.strip(),"en")
    except Exception as e:
        print('Can\'t get car mark. Reason %s.' % e)
        return ""
#коробка передач
def get_transmission(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        if(rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(2) > td:nth-child(4)")[0].text.strip())=="자동"):
            return "Автомат"
        else:
            return "Механика"
    except Exception as e:
        print('Can\'t get transmission. Reason %s.' % e)
        return ""
#фото авто
def get_img_src(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        div = soup.find_all("div", class_="vehicle-thumbnail")
        items = div[0].find_all("img")
        images = []
        for item in items:
            if item.has_attr('src'):
                images.append(item.get('src'))
        return images
    except Exception as e:
        print('Can\'t get img src. Reason %s.' % e)
        return ""
def get_img_str(imgs, link):
    try:
        title = parse(link, get_car_title)
        img_str = ''
        for img in imgs :
            str_arr = str(img).split('/')
            str_arr.reverse()
            img_str += str_arr[0]+'[:param:][alt='+title+'][title='+title+']|'
        return img_str[:-1]
    except Exception as e:
        print('Can\'t get Images str. Reason %s' % e)
        return ""
#двигатель
def get_car_displacement(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.findall("[0-9]+", rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(5) > td:nth-child(4)")[0].text.strip()))
        return "".join(res)
    except Exception as e:
        print('Can\'t get car displacement. Reason %s.' % e)
        return ""
#название авто
def get_car_title(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return rm_new_line(ko_translate(soup.find("h2", class_="tit").text.strip(), "en"))
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#лот аукциона
def get_lot_id(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return rm_new_line(ko_translate(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-tit > p > strong")[0].text.strip(), "en"))
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#год автомобиля
def get_car_year(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.match("[0-9]+", rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(1) > td:nth-child(2)")[0].text.strip()))
        if(res):
            return res.group()
    except Exception as e:
        print('Can\'t get car year. Reason %s.' % e)
        return ""
#цвет автомобиля
def get_car_color(html):
    color_data = {
        "흰색":"Белый",
        "은색":"Серебро",
        "검정":"Черный",
        "기타":"Так далее",
        "빨간":"Красный",
        "보라색":"Фиолетовый",
        "주황색":"Оранжевый",
        "초록":"Зеленый",
        "회색":"Серый",
        "금":"Золото",
        "푸른":"Голубой",
        "베이지":"Бежевый",
        "진주":"Жемчужина"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return color_data[re.sub("\s.+","", rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(3) > td:nth-child(4)")[0].text.strip()))]
    except Exception as e:
        print('Can\'t get car color. Reason %s.' % e)
        return ""
#тип автомобиля
def get_car_type(html):
    car_type_data = {
        "승용 (7인승)":"Универсал",
        "승용 (11인승)":"Фургон",
        "승합 (12인승)":"Фура",
        # "Трактор",
        "승용 (5인승)":"Седан",
        # "Родстер",
        # "Пикап",
        # "Мотоцикл",
        "승용 (9인승)":"Минивен",
        "승용 (4인승)":"Хэтчбек",
        "승용/SUV": "Кроссовер",
        "승용 (2인승)":"Купе",
        # "Кабриолет",
        # "Багги"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return car_type_data[rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(6) > td:nth-child(2)")[0].text.strip())]
    except Exception as e:
        print('Can\'t get car type. Reason %s.' % e)
        return ""
#запись айди авто в файл
def write_car_id(file_name, data):
    try:
        return open(file_name, "a").write(data)
    except Exception as e:
        print('Can\'t wrine file %s. Reason %s.' % (file_name,e))
#цена автомобиля 
def get_car_price(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return rm_new_line(soup.select("body > div.page-popup.exhibited-vehicle > div.clfix > div.vehicle-info > p > strong > em")[0].text.strip())
    except Exception as e:
        print('Can\'t get car price. Reason %s.' % e)
        return ""
#описание авто 
def get_car_description(html):
    description = ''
    soup = BeautifulSoup(html, 'html.parser')
    table = soup.find_all("table", class_="tbl-v02")
    div = soup.find_all("div", class_="car-status-map")
    video = soup.find("div", id="yesMovie")
    if div:
        try:
            description += '<h2 class="page-subtit mt60">Протокол осмотра автомобилей, выставленных на аукцион</h2>'
            description += text_len(str(table[0]), "ru")
        except Exception as e:
            print('Can\'t get car description "Performance Evaluation Information". Reason %s.' % e)
        try:
            description += '<h2 class="page-subtit mt60">Детали автомобиля</h2>'
            description += text_len(str(table[1]), "ru")
        except Exception as e:
            print('Can\'t get car description "Option information". Reason %s.' % e)
        try:   
            description += '<h2 class="page-subtit mt60">Состояние кузова автомобиля</h2>'
            description += text_len(str(div[0]), "ru")
            description += text_len(str(table[2]), "ru")
        except Exception as e:
            print('Can\'t get car descritpion comm list. Reason %s.' % e)
        try:
            description += '<h2 class="page-subtit mt60">Видео автомобиля</h2>'
            description += text_len(str(video), "ru")
        except Exception as e:
            print('Can\'t get car description image. Reason %s.' % e)
    return description
#категория для авто
# def get_car_category(html):
#     try:
#         categoty_data = {
#             "Genesis":"Genesis",
#             "Kia":"Kia Motors",
#             "Hyundai":"Hyundai",
#             "Ssangyong":"SsangYong",
#             "Renault":"Renault",
#             "Benz":"Mercedes Benz",
#             "Chevrolet":"Chevrolet",
#             "Jaguar":"Jaguar",
#             "BMW":"BMW",
#             "Land":"Land Rover",
#             "Peugeot":"Peugeot",
#             "Volkswagen":"Volkswagen",
#             "Ford":"Ford"
#         }
#         return categoty_data[re.match("(^\w*)", get_car_title(html)).group(0)]
#     except Exception as e:
#         print('Can\'t get car category. Reason %s.' % e)
#         return ""
def get_car_category_url(html):
    try:
        categoty_url_data = {
            "Genesis":"genesis",
            "Kia Motors":"kia-motors",
            "Hyundai":"hyundai",
            "SsangYong":"ssangyong",
            "Renault":"renault",
            "Mercedes Benz":"mercedes-benz",
            "Chevrolet":"chevrolet",
            "Jaguar":"jaguar",
            "BMW":"bmw",
            "Land Rover":"land-rover",
            "Peugeot":"peugeot",
            "Volkswagen":"volkswagen",
            "Ford":"ford"
        }
        return categoty_url_data[config['DEFAULT']['CATEGORY']]
    except Exception as e:
        print('Can\'t get car category url. Reason %s.' % e)
        return ""
#ID авто для парсингу
def get_car_id(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        items = soup.find_all("td", class_="align-l")
        for item in items:
            car_id = re.search('"([A-Z]{2})"."([A-Z]{2}\d{12})"."(\d)"', item.find("p", class_="txt").find("a", class_="a_list").get('onclick'))
            if car_id:
                write_car_id("car_id2.txt",car_id.group(1)+"|"+car_id.group(2)+"|"+car_id.group(3)+"\n")
    except Exception as e:
        print('Can\'t get car id. Reason %s.' % e)
#парсимо сайт із сесії
def parse(link, func):
    # login(login_link)
    try:
        html = session.get(link)
        if html.status_code == 200:
            return func(html.text)
        else:
            print('Error code %d', html.status_code)
    except Exception as e:
        print('Can\'t get html from site. Reason %s.' % e)
#парсимо всі ссилки на авто по сторінкам
def get_pages(page_start=1, page_end=2, page_unit=20, search_maker="KI"):
    for i in range(page_start,page_end+1):
        unf = uniform(1,3)
        time.sleep(unf)
        print("Page %d. Sleep %d." % (i, unf))
        parse(config['DEFAULT']['URL']+"&set_search_maker="+str(search_maker)+"&searchPageUnit="+str(page_unit)+"&pageIndex="+str(i), get_car_id)
    pass
#складаємо всі ссилки на авто в один файл
def get_all_links(car_link):
    data = read_file("car_id2.txt", "\n")
    res = list(map(lambda x: x.split('|'), data[:-1]))
    return list(map(lambda x: '{}?refererMode=login&searchMngDivCd={}&searchMngNo={}&searchExhiRegiSeq={}'.format(car_link, x[0], x[1], x[2]), res))
#закачуємо усі картинки
def download_images(img_urls):
    try:
        for img_url in img_urls:
            str_arr = str(img_url).split('/')
            str_arr.reverse()
            img_data = requests.get(img_url).content
            with open('./py_img2/' + str_arr[0], 'wb') as handler:
                handler.write(img_data)
    except Exception as e:
        print('Failed download image. Reason: %s' % e)
def rm_csv(name = "data2.csv"):
        try:
            os.remove(name)
            print("File Removed!")
        except Exception as e:
                print('Failed to remove file "data2.csv". Reason: %s' % e)
def create_csv(name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try:
        with open(path, 'w', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow(['Категория', 'URL категории', 'Товар', 'Вариант', 'Описание', 'Цена', 'URL', 'Изображение', 'Артикул', 'Количество', 'Активность', 'Заголовок [SEO]', 'Ключевые слова [SEO]', 'Описание [SEO]', 'Старая цена', 'Рекомендуемый', 'Новый', 'Сортировка', 'Вес', 'Связанные артикулы', 'Смежные категории', 'Ссылка на товар', 'Валюта', 'Свойства'])
    except Exception as e:
            print('Failed to create file "data.csv". Reason: %s' % e)
#записуємо усі данні у файл
def write_csv(data, name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try: 
        with open(path, 'a', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow([data['category'], data['category_url'], data['title'], ' ', data['description'], data['price'], ' ', data['images'], data['article'], data['count'], data['activation'], data['title-seo'], ' ', ' ', ' ', data['recomended'], data['new'], ' ', ' ', ' ', ' ', ' ', data['currency'], data['properties']])
    except Exception as e:
        print('Can\'t write csv file. Reason %s' % e)
#створюємо папку
def create_folder(path):
    try:
        if not os.path.exists(path):
            os.makedirs(path)
    except Exception as e:
        print('Can\'t create folder in path %s. Reason %s' % path, e)
#очищаємо усі файли із папки
def clear_folder(folder):
    for filename in os.listdir(folder):
        file_path = os.path.join(folder, filename)
        try:
            if os.path.isfile(file_path) or os.path.islink(file_path):
                os.unlink(file_path)
            elif os.path.isdir(file_path):
                shutil.rmtree(file_path)
        except Exception as e:
            print('Failed to delete %s. Reason: %s' % (file_path, e))
#витягуємо всі данні на авто
def get_car(link):
    time.sleep(uniform(3,6))
    html = get_shtml(link)
    try:
        if(isinstance(html, int)):
            print(html)
        else:
            car = {}
            color = get_car_color(html)
            car_type = get_car_type(html)
            distance_driven = get_distance_driven(html)
            displacement = get_car_displacement(html)
            year = get_car_year(html)
            transmission = get_transmission(html)
            fuel = get_fuel(html)
            lot_number = get_lot_id(html)
            mark = get_car_mark(html)
            category = config['DEFAULT']['CATEGORY']

            car['category'] = category
            car['category_url'] = get_car_category_url(html)
            car['title'] = get_car_title(html)
            car['title-seo'] = get_car_title(html)
            car['price'] = get_car_price(html)
            car['description'] = get_car_description(html)
            car['images'] = get_img_str(get_img_src(html), link)
            car['count'] = '-1'
            car['activation'] = '1'
            car['currency'] = 'USD'
            car['recomended'] = '0'
            car['new'] = '0'
            car['article'] = get_lot_id(html)
            car['properties'] = 'Цвет=[type=assortmentCheckBox value=%s product_margin=Желтый|Белый|Серебро|Красный|Фиолетовый|Оранжевый|Зеленый|Серый|Золото|Коричневый|Голубой|Черный|Бежевый]&Кузов=[type=assortmentCheckBox value=%s product_margin=Универсал|Фургон|Фура|Трактор|Седан|Родстер|Пикап|Мотоцикл|Минивен|Хэтчбек|Кроссовер|Купе|Кабриолет|Багги]&Пробег=%s&Двигатель=%s&Год=%s&Трансмиссия=[type=assortmentCheckBox value=%s product_margin=Механика|Автомат]&Топливо=[type=assortmentCheckBox value=%s product_margin=Дизель|Бензин|Газ]&Модель=%s&Марка=%s&Номер лота=%s&Аукцион=sellcarauction' % (color, car_type, distance_driven, displacement, year, transmission, fuel, mark, category, lot_number)

            download_images(get_img_src(html))

            return write_csv(car)
    except Exception as e:
        print('Can\'t find element. Reason: %s' % e)
def get_currency(): 
    try:
        html = get_shtml("https://www.currencyconverterx.com/KRW/USD/1000000")
        soup = BeautifulSoup(html, 'html.parser')
        return soup.select("#converterForm > div.convert-form__result > strong > span.convert-form__c3")[0].text.strip()
    except Exception as e:
        print('Can\'t get currency. Reason: %s' % e)
# for i in range(264): 
#     time.sleep(uniform(3,6))
#     proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) }
#     useragent = {'User-Agent': fake_useragent.UserAgent().random}
    # url = "http://sitespy.ru/my-ip"
    # html = get_html(url, headers, proxy)
    # if(type(html)==int):
    #     print(html)
    # else: 
    #     print(get_ip(html))  
    # print(proxy)

def main():
    time_start = time.time()
    # get_pages()
    # rm_csv()
    # create_csv()
    # create_folder('./py_img2')
    # clear_folder('./py_img2/')
    my_list = get_all_links(config['DEFAULT']['CAR_LINK'])
    # get_car(my_list[0])
    # print(get_currency())
    with Pool(5) as p:
        p.map(get_car, my_list) 
    # for i in range(0,10):
    #     #time.sleep(uniform(3,6))
    #     get_car(my_list[i])
    #     print("Авто ", i)
    #get_pages(1, 117)
    time_end = time.time() 
    print(time_end - time_start)
if __name__ == '__main__':
    main()