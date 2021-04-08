# -*- coding: utf-8 -*-
import time
import csv
import requests
import os
import shutil
import re
import html
import fake_useragent
import configparser
import mysql.connector as mysql
from ftplib import FTP
from io import BytesIO
from requests.auth import HTTPBasicAuth
from deep_translator import GoogleTranslator
from multiprocessing import current_process, Pool
from bs4 import BeautifulSoup
from random import choice
from random import uniform
from requests_html import HTMLSession

#підтягуємо усі настройки з файлу settings.ini
config = configparser.ConfigParser(allow_no_value=True)
config.read("settings.ini") 
if(config['LOTTE']['AUTOCODE'] == '2'):
    try:
        db_connection = mysql.connect(host="185.74.252.12", database="olegk202_kdm", user="olegk202_kdm", password="Kostiuk_6173")
    except Exception as e:
        print(e)

if(config['LOTTE']['FTPCODE'] == '1'):
    ftp = FTP("185.74.252.12")
    ftp.login("olegk202","lYgH51teND")
    ftp.cwd("public_html/uploads/tempimage")
#read file function 
def read_file(file_name, delimiter):
    try:
        return open(file_name).read().split(delimiter)
    except Exception as e:
        print('Can\'t read %s. Reason %s.' % (file_name, e))
#підставляємо кожен раз іншого юзер агента
user = fake_useragent.UserAgent().random
#включаємо сесію
session = requests.Session()

headers = {'Authorization':'Basic MTUyMDAwOjQyNzU=', 'user-agent': user, 'accept': '*/*'}
#set cookies
cookie = '_xm_webid_1_={}; hpAuctSaveid=152000; JSESSIONID={}'.format(config['LOTTE']['WEB_ID'], config['LOTTE']['SESSION_ID'])
session.headers.update({'Authorization':'Basic MTUyMDAwOjQyNzU=', 'user-agent': user, 'Cookie': cookie}) 
#parsing from proxy
proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) }
session.proxies.update(proxy)    

#translate function 
def ko_translate(text, lan):
    try:
        if(len(text)>1 and len(text)<5000 and isinstance(text,str)):
            return GoogleTranslator(source='ko', target=lan).translate(text)
    except Exception as e:
        print('Failed to Translate text. Reason: %s' % e)
        res = ''
        return res

#удаляємо всі зайві пробіли, переноси строк і табуляції
def rm_new_line(string):
        try:
            string = html.unescape(string)
            string = re.sub("\r?\n","",string)
            string = string.replace("> <", "><")
            string = string.replace("- -", "--")
            string = string.replace("< ", "<")
            string = string.replace(" >", ">")
            string = string.replace("/ ", "/")
            string = string.replace("<! ", "<!")
            string = re.sub("\<[!\s-][\\a-z\S\s]*[^<>]*[\-\s]\>", "", string)
            string = re.sub("\<[!\s-][\S]+[\-\s]\>", "", string)
            string = string.replace("\s{2,}", " ")
            return string
        except Exception as e:
                print('Failed to delete White Space Tabs and New Line in string "%s". Reason: %s' % (string, e))
#удаляємо всі зайві пробіли, ненужні символи у назві картинки
def clear_img_name(img_name):
        try:
            img_name = img_name.split(".")
            if(len(img_name)==2):
                img_name[0] = img_name[0].lower()
                img_name[0] = re.sub("\(","",img_name[0])
                img_name[0] = re.sub("\)","",img_name[0])
                img_name[0] = re.sub("\{","",img_name[0])
                img_name[0] = re.sub("\}","",img_name[0])
                img_name[0] = re.sub("\[","",img_name[0])
                img_name[0] = re.sub("\]","",img_name[0])
                img_name[0] = re.sub("\<","",img_name[0])
                img_name[0] = re.sub("\>","",img_name[0])
                img_name[0] = re.sub("\$","",img_name[0])
                img_name[0] = re.sub("\#","",img_name[0])
                img_name[0] = re.sub("\%","",img_name[0])
                img_name[0] = re.sub("\~","",img_name[0])
                img_name[0] = re.sub("\@","",img_name[0])
                img_name[0] = re.sub("\^","",img_name[0])
                img_name[0] = re.sub("\&","",img_name[0])
                img_name[0] = re.sub("\*","",img_name[0])
                img_name[0] = re.sub("\+","",img_name[0])
                img_name[0] = re.sub("\=","",img_name[0])
                img_name[0] = re.sub("\,","",img_name[0])
                img_name[0] = re.sub("\"","",img_name[0])
                img_name[0] = re.sub("\'","",img_name[0])
                img_name[0] = re.sub("\/","",img_name[0])
                img_name[0] = re.sub("-","_",img_name[0])
                img_name[0] = re.sub("\s","_",img_name[0])
                img_name = ".".join(img_name)
            else:
                ext = img_name[-1]
                name = "_".join(img_name[:-1])
                img_name = name+"."+ext
            return img_name
        except Exception as e:
                print('Failed to clear image name. Reason: %s' % e)
def clear_car_name(car_name):
        try:
            if(isinstance(car_name, str) and car_name.strip() != ''):
                car_name = re.sub("\.","-",car_name)
                car_name = re.sub("\(","",car_name)
                car_name = re.sub("\)","",car_name)
                car_name = re.sub("\{","",car_name)
                car_name = re.sub("\}","",car_name)
                car_name = re.sub("\[","",car_name)
                car_name = re.sub("\]","",car_name)
                car_name = re.sub("\<","",car_name)
                car_name = re.sub("\>","",car_name)
                car_name = re.sub("\$","",car_name)
                car_name = re.sub("\#","",car_name)
                car_name = re.sub("\%","",car_name)
                car_name = re.sub("\~","",car_name)
                car_name = re.sub("\@","",car_name)
                car_name = re.sub("\^","",car_name)
                car_name = re.sub("\&","",car_name)
                car_name = re.sub("\*","",car_name)
                car_name = re.sub("\+","",car_name)
                car_name = re.sub("\=","",car_name)
                car_name = re.sub("\,","",car_name)
                car_name = re.sub("\"","",car_name)
                car_name = re.sub("\'","",car_name)
                car_name = re.sub("\/","",car_name)
            return car_name
        except Exception as e:
                print('Failed to clear car name. Reason: %s' % e)
#следит чтоб небило длины текста более 5000 символов
def text_len(text, lang):
    new_text = ''
    if(isinstance(text,str) and len(text)>1):
        if(len(text)>5000):
            i = 0
            while (i <= int(len(text)/4999)):
                if(i == int(len(text)/4999)):
                    new_text += ko_translate(text[i*4999:len(text)],lang)
                else:        
                    new_text += ko_translate(text[i*4999:i+1*4999], lang)
                i+=1
        else: 
            new_text = ko_translate(text, lang)
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
#витягуємо максимальне число сторінок
def get_max_page(html, pages=20):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        if(int(soup.select("body > div.layout-wrap > div.layout-container > div.layout-content > div.exhibition-list-tbl > div.tbl-top > p:nth-child(2) > span > em")[0].text.strip()) % pages > 0):
            return int(int(soup.select("body > div.layout-wrap > div.layout-container > div.layout-content > div.exhibition-list-tbl > div.tbl-top > p:nth-child(2) > span > em")[0].text.strip())/pages) + 1
        else:
            return int(int(soup.select("body > div.layout-wrap > div.layout-container > div.layout-content > div.exhibition-list-tbl > div.tbl-top > p:nth-child(2) > span > em")[0].text.strip())/pages)
    except Exception as e:
        print('Can\'t get max page. Reason %s' % e)
#витягуємо html із сесію
def get_shtml(link):
    try:
        # res = re.findall("{'JSESSIONID': '([\d\w=.]+)', '_xm_webid_1_': '([\d]+)'}", str(login(login_link)))
        # cookie = '_xm_webid_1_={}; _ga=GA1.2.2027634233.1611498833; _gid=GA1.2.1777522609.1612255912; hpAuctSaveid=152000; JSESSIONID={}; _gat_gtag_UA_118654321_1='.format(res[0][1], res[0][0]) 
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
        res = re.findall("[0-9]+", str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(1) > td:nth-child(4)")).strip())
        return int("".join(res))
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
        return fulel_data[str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(4) > td:nth-child(4)")[0].text.strip())]
    except Exception as e:
        print('Can\'t get fuel. Reason %s.' % e)
        return ""
#марка авто
def get_car_mark(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        string = text_len(str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > div > div > table > tbody > tr:nth-child(4) > td:nth-child(5)")[0].text.strip()),"en")
        string = re.sub("\s?\?\s?","",string)
        return string
    except Exception as e:
        print('Can\'t get car mark. Reason %s.' % e)
        return ""
#vin номер авто
def get_car_vin(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > div > div > table > tbody > tr:nth-child(7) > td")[0].text.strip())
    except Exception as e:
        print('Can\'t get car vin code. Reason %s.' % e)
        return ""
#коробка передач
def get_transmission(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        if(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(2) > td:nth-child(4)")[0].text.strip() == "자동"):
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
        div = soup.find_all("div", class_="vehicle-thumbnail-detail")
        items = div[0].find_all("img")
        images = []
        for item in items:
            if item.has_attr('src'):
                images.append(item.get('src'))
        return images
    except Exception as e:
        print('Can\'t get img src. Reason %s.' % e)
        return ""
def get_img_str(imgs, html):
    try:
        title = get_car_title(html)
        img_str = ''
        for img in imgs :
            # str_arr = str(img).split('/')
            # str_arr.reverse()
            # img_name = clear_img_name(str_arr[0])
            img_str += img+'[:param:][alt='+title+'][title='+title+']|'
        return img_str[:-1]
    except Exception as e:
        print('Can\'t get Images str. Reason %s' % e)
        return ""
#двигатель
def get_car_displacement(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.findall("[0-9]+", str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(5) > td:nth-child(4)")[0].text.strip()))
        return int("".join(res))
    except Exception as e:
        print('Can\'t get car displacement. Reason %s.' % e)
        return ""
#лот аукциона
def get_lot_id(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return ko_translate(rm_new_line(str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-tit > p > strong")[0].text.strip())), "en")
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#название авто
def get_car_title(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        title = ko_translate(rm_new_line(str(soup.find("h2", class_="tit").text.strip())), "en")
        return get_lot_id(html) + " " + clear_car_name(title)
    except Exception as e:
        print('Can\'t get title. Reason %s.' % e)
        return ""
#витягуємо оцінку авто
def get_car_estimate(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return ko_translate(rm_new_line(str(soup.select("body > div.page-popup.exhibited-vehicle > div.clfix > div.vehicle-info > ul > li:nth-child(4) > strong")[0].text.strip())), "en")
    except Exception as e:
        print('Can\'t get car estimate. Reason %s.' % e)
        return ""
#год автомобиля
def get_car_year(html):
    soup = BeautifulSoup(html, 'html.parser')
    try:
        res = re.match("[0-9]+", str(soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(1) > td:nth-child(2)")[0].text.strip()))
        if(res):
            return int(res.group())
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
        return color_data[re.sub("\s.+","", soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(3) > td:nth-child(4)")[0].text.strip())]
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
        "픽업":"Пикап",
        # "Мотоцикл",
        "화물":"Фрахт",
        "승용 (9인승)":"Минивен",
        "승용 (4인승)":"Хэтчбек",
        "승용/SUV": "Кроссовер",
        "승용 (2인승)":"Купе",
        "승합":"Кабриолет"
        # "Багги"
    }
    soup = BeautifulSoup(html, 'html.parser')
    try:
        return car_type_data[soup.select("body > div.page-popup.exhibited-vehicle > div.vehicle-detail > div > div.vehicle-detail > div > table > tbody > tr:nth-child(6) > td:nth-child(2)")[0].text.strip()]
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
        return int((float("".join(soup.select("body > div.page-popup.exhibited-vehicle > div.clfix > div.vehicle-info > p > strong > em")[0].text.strip().split(",")))*10000)/float(config['LOTTE']['USDCOURSE']))
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
            description += text_len(rm_new_line(str(table[0]).strip()), "ru")
        except Exception as e:
            print('Can\'t get car description "Performance Evaluation Information". Reason %s.' % e)
        try:
            description += '<h2 class="page-subtit mt60">Детали автомобиля</h2>'
            description += text_len(rm_new_line(str(table[1]).strip()), "ru")
        except Exception as e:
            print('Can\'t get car description "Option information". Reason %s.' % e)
        try:   
            description += '<h2 class="page-subtit mt60">Состояние кузова автомобиля</h2>'
            description += text_len(rm_new_line(str(div[0]).strip()), "ru")
            description += text_len(rm_new_line(str(table[2]).strip()), "ru")
        except Exception as e:
            print('Can\'t get car descritpion comm list. Reason %s.' % e)
        try:
            description += '<h2 class="page-subtit mt60">Видео автомобиля</h2>'
            description += text_len(rm_new_line(str(video).strip()), "ru")
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
#             "Ford":"Ford",
#             "Audi":"Audi",
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
            "Ford":"ford",
            "Audi":"audi",
            "Bentley":"bentley",
            "Cadillac":"cadillac"
        }
        return categoty_url_data[config['LOTTE']['CATEGORY']]
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
#ID нових авто, что добавились, для парсингу 
def get_missed_car_id(html):
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
    #login(config['LOTTE']['LOGIN_LINK'])
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
        parse(config['LOTTE']['URL']+"?searchPageUnit="+str(page_unit)+"&pageIndex="+str(i)+"&set_search_maker="+str(search_maker), get_car_id)
    pass
#складаємо всі ссилки на авто в один файл
def get_all_links(car_link, file_name="car_id2.txt"):
    data = read_file(file_name, "\n")
    res = list(map(lambda x: x.split('|'), data[:-1]))
    return list(map(lambda x: '{}?refererMode=login&searchMngDivCd={}&searchMngNo={}&searchExhiRegiSeq={}'.format(car_link, x[0], x[1], x[2]), res))
#закачуємо усі картинки
def download_images(img_urls, folder_name='py_img2'):
    try:
        for img_url in img_urls:
            str_arr = str(img_url).split('/')
            str_arr.reverse()
            img_name = clear_img_name(str_arr[0])
            if(config['LOTTE']['FTPCODE'] == '1'):
                img_data = BytesIO(requests.get(img_url).content) #without BytesIO if you whant download to local machine
                ftpCommand = "STOR %s"%img_name #comment if you whant download to local machine
                ftp.storbinary(ftpCommand, fp=img_data) #comment if you whant download to local machine
            else:
                img_data = requests.get(img_url).content
                with open('./'+folder_name+'/' + img_name, 'wb') as handler:
                    handler.write(img_data)
    except Exception as e:
        print('Failed download image. Reason: %s' % e)
def rm_csv(name = "data2.csv"):
        try:
            if(os.path.exists(name)):
                os.remove(name)
            print("File %s Removed!" % name)
        except Exception as e:
                print('Failed to remove file %s. Reason: %s' % (name, e))
def create_csv(name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try:
        with open(path, 'w', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow(['Категория', 'URL категории', 'Товар', 'Вариант', 'Описание', 'Цена', 'URL', 'Изображение', 'Артикул', 'Количество', 'Активность', 'Заголовок [SEO]', 'Ключевые слова [SEO]', 'Описание [SEO]', 'Старая цена', 'Рекомендуемый', 'Новый', 'Сортировка', 'Вес', 'Связанные артикулы', 'Смежные категории', 'Ссылка на товар', 'Валюта', 'Свойства'])
    except Exception as e:
            print('Failed to create file %s. Reason: %s' % (name, e))
def create_txt(name):
    path = os.getcwd() + os.path.sep + name
    try:
        open(path, 'w', encoding="utf-8", newline='')
    except Exception as e:
            print('Failed to create file %s. Reason: %s' % (name, e))
#записуємо усі данні у файл
def write_csv(data, name = "data2.csv"):
    path = os.getcwd() + os.path.sep + name
    try: 
        with open(path, 'a', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow([data['category'], data['category_url'], data['title'], ' ', data['description'], data['price'], ' ', data['images'], data['article'], data['count'], data['activation'], data['title-seo'], ' ', ' ', ' ', data['recomended'], data['new'], ' ', ' ', ' ', ' ', ' ', data['currency'], data['properties']])
    except Exception as e:
        print('Can\'t write %s file. Reason %s' % (name, e))
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
#витягуємо курс валют 
def get_currency(): 
    try:
        html = get_shtml("https://www.currencyconverterx.com/KRW/USD/1000000")
        soup = BeautifulSoup(html, 'html.parser')
        return float(soup.select("#converterForm > div.convert-form__result > strong > span.convert-form__c3")[0].text.strip())
    except Exception as e:
        print('Can\'t get currency. Reason: %s' % e)
def fetch(url):
    # p = current_process()
    # if(p.name != 'MainProcess' and p._identity[0] and os.getpid()):
    #     print('process counter:', p._identity[0], 'pid:', os.getpid())
    asession = HTMLSession()
    asession.headers.update({'User-Agent': fake_useragent.UserAgent().random})
    asession.max_redirects = 60
    #parsing from proxy
    # proxy = { 'http': 'http://' + choice(read_file("proxies.txt","\n")) +'/' }
    # asession.proxies.update(proxy)
    unf = uniform(1,4)
    data = ''
    time.sleep(unf)
    try:
        login_data = {
            "userId": "152000",
            "userPwd": "4275"
            }
        r = asession.request('POST', url, login_data, allow_redirects=False)
    except Exception as e:
        print('Failed to get page %s. Reason: %s' % (url, e))
    try:
        r.html.render(timeout=600)
        if(r.status_code == 200):
            data = r.headers
    except Exception as e:
        print('Failed to render page %s. Reason: %s' % (url, e))
    try:
        asession.close()
    except Exception as e:
        print('Failed to close session %s. Reason: %s' % (url, e))
    return data
krw_to_usd = get_currency()
#витягуємо всі данні на авто
def get_car(link):
    # p = current_process()
    # if(p.name != 'MainProcess'):
    #     print('process counter:', p._identity[0], 'pid:', os.getpid())
    time.sleep(uniform(3,8))
    html = get_shtml(link)
    try:
        if(isinstance(html, int)):
            print(html)
        else:
            car = {}
            year = get_car_year(html)
            if(year < 2012):
                return
            color = get_car_color(html)
            car_type = get_car_type(html)
            distance_driven = get_distance_driven(html)
            displacement = get_car_displacement(html)
            transmission = get_transmission(html)
            fuel = get_fuel(html)
            lot_number = get_lot_id(html)
            car_vin = get_car_vin(html)
            car_estimate = get_car_estimate(html)
            category = config['LOTTE']['CATEGORY']
            mark = category + " " + get_car_mark(html).upper()
            car['category'] = category
            car['category_url'] = get_car_category_url(html)
            car['title'] = get_car_title(html)
            car['title-seo'] = car['title']
            car['price'] = get_car_price(html)
            car['description'] = rm_new_line(get_car_description(html))
            car['images'] = get_img_str(get_img_src(html), html)
            car['count'] = '0'
            car['activation'] = '1'
            car['currency'] = 'USD'
            car['recomended'] = '0'
            car['new'] = '0'
            car['article'] = lot_number
            car['properties'] = 'Цвет=[type=assortmentCheckBox value=%s product_margin=Желтый|Белый|Серебро|Красный|Фиолетовый|Оранжевый|Зеленый|Серый|Золото|Коричневый|Голубой|Черный|Бежевый]&Кузов=[type=assortmentCheckBox value=%s product_margin=Универсал|Фургон|Фура|Трактор|Седан|Родстер|Пикап|Мотоцикл|Минивен|Хэтчбек|Кроссовер|Купе|Кабриолет|Багги]&Пробег=%s&Двигатель=%s&Год=%s&Трансмиссия=[type=assortmentCheckBox value=%s product_margin=Механика|Автомат]&Топливо=[type=assortmentCheckBox value=%s product_margin=Дизель|Бензин|Газ]&Модель=%s&Марка=%s&Номер лота=%s&Оценка автомобиля=%s&VIN номер=%s&Аукцион=lotteautoauction %s' % (color, car_type, distance_driven, displacement, year, transmission, fuel, mark, category, lot_number, car_estimate, car_vin, config['LOTTE']['AUCTIONDATE'])
            #download_images(get_img_src(html))
            return write_csv(car)
    except Exception as e:
        print('Can\'t find element. Reason: %s' % e)
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
    print(fetch('https://www.lotteautoauction.net/hp/auct/cmm/viewLoginUsr.do?loginMode=redirect'))
    if(config['LOTTE']['AUTOCODE'] == '0'):
        rm_csv("car_id2.txt")
        create_txt("car_id2.txt")
        if(config['LOTTE']['CATEGORY'] == "Renault"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=SS&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'SS')
        elif(config['LOTTE']['CATEGORY'] == "Kia Motors"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=KI&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'KI')
        elif(config['LOTTE']['CATEGORY'] == "Hyundai"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=HD&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'HD')
        elif(config['LOTTE']['CATEGORY'] == "BMW"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=BM&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'BM')
        elif(config['LOTTE']['CATEGORY'] == "Audi"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=AD&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'AD')
        elif(config['LOTTE']['CATEGORY'] == "Chevrolet"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=DW&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'DW')
        elif(config['LOTTE']['CATEGORY'] == "SsangYong"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=SY&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'SY')
        elif(config['LOTTE']['CATEGORY'] == "Bentley"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=BE&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'BE')
        elif(config['LOTTE']['CATEGORY'] == "Cadillac"):
            s_page = parse(config['LOTTE']['URL']+"?set_search_maker=CA&searchPageUnit=20&pageIndex=1", get_max_page)
            get_pages(int(config['LOTTE']['FPAGE']), s_page, 20, 'CA')
    elif(config['LOTTE']['AUTOCODE'] == '1'):
        rm_csv()
        create_csv()
        # create_folder('./py_img2')
        # clear_folder('./py_img2/')
        my_list = get_all_links(config['LOTTE']['CAR_LINK'])
        # get_car(my_list[0])
        with Pool(40) as p:
            p.map(get_car, my_list) 
        if(config['LOTTE']['FTPCODE'] == '1'):
            ftp.quit()
    elif(config['LOTTE']['AUTOCODE'] == '2'):
        select_car_query = "SELECT title FROM mg_product WHERE title LIKE '%Genesis EQ900%';"
        with db_connection.cursor() as cursor:
            cursor.execute(select_car_query)
            result = cursor.fetchall()
            for row in result:
                print(row[0])
    time_end = time.time() 
    print(time_end - time_start)
if __name__ == '__main__':
    main()