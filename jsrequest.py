# -*- coding: utf-8 -*-
from requests_html import HTMLSession, AsyncHTMLSession
from concurrent.futures import ThreadPoolExecutor
from multiprocessing import current_process, Pool
from deep_translator import GoogleTranslator
from random import uniform
from random import choice
import asyncio
import time
import re
import os
import csv
import html
import fake_useragent

DATE = "14/04/2021"
USD = "1110"
PARSE_CODE = 1
MAX_PAGE = 74
#read file function 
def read_file(file_name, delimiter):
    try:
        return open(file_name).read().split(delimiter)
    except Exception as e:
        print('Can\'t read %s. Reason %s.' % (file_name, e))

car_link = "http://www.sellcarauction.co.kr/newfront/onlineAuc/on/onlineAuc_on_detail.do"
url = "http://www.sellcarauction.co.kr/newfront/receive/rc/receive_rc_list.do"

headers = {'User-Agent':'Mozilla/5.0(X11; Ubuntu; Linux x86_64; rv:66.0)Gecko/20100101 Firefox/66.0'}
login_data = { 
        "identity": "",
        "i_sPswd": "9977",
        "i_sUserId": "546801"
    }

#translate function 
def ko_translate(text, lan):
    try:
        if(len(text)>1 and len(text)<5000 and isinstance(text,str)):
            return GoogleTranslator(source='ko', target=lan).translate(text)
    except Exception as e:
        print('Failed to Translate text. Reason: %s' % e)
        res = ''
        return res

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

#удаляємо всі зайві пробіли, переноси строк і табуляції
def rm_new_line(string):
        try:
            string = html.unescape(string)
            string = re.sub("\s?\r?\n", "", string)
            string = re.sub("\>\s+\<", "><", string)
            string = re.sub("\<\s+", "<", string)
            string = re.sub("\s+\>", ">", string)
            string = re.sub("\/\s+", "/", string)
            string = re.sub("\-\s+\-", "--", string)
            string = re.sub("\<\!\s+", "<!", string)
            string = re.sub("\<[!\s-][\\a-z\S\s]*[^<>]*[\-\s]\>", "", string)
            string = re.sub("\<[!\s-][\S]+[\-\s]\>", "", string)
            string = re.sub("\s{2,}", " ", string)
            return string
        except Exception as e:
            print('Failed to delete White Space Tabs and New Line. Reason: %s' % e)

def clear_car_name(car_name):
        try:
            if(isinstance(car_name, str) and car_name != ''):
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
    unf = uniform(1,6)
    data = ''
    time.sleep(unf)
    try:
        r = asession.request('GET', url, allow_redirects=False)
    except Exception as e:
        print('Failed to get page %s. Reason: %s' % (url, e))
    try:
        if(r.status_code == 200):
            r.html.render(timeout=600)
            data = r.html
    except Exception as e:
        print('Failed to render page %s. Reason: %s' % (url, e))
    try:
        asession.close()
    except Exception as e:
        print('Failed to close session %s. Reason: %s' % (url, e))
    return data

def get_car_img(html):
    data = ''
    if(html != ""):
        for i in range(len(html.find("#realImg>div"))):
            try:
                html.find("#realImg>div")[i].attrs
            except Exception as e:
                print('Can\'t div#realImg>div[i] attrs. Reason %s.' % e)
            if "style" in html.find("#realImg>div")[i].attrs:
                if html.find("#realImg>div")[i].attrs["style"] != "display:none":
                    data += "<div class='p-3'>"
                    try:
                        data += "<div class='korea-auc-damage aj type-"+html.find("div#realImg>div")[i].attrs["class"][0]+" position-relative'>"
                    except Exception as e:
                        print('Can\'t div#realImg>div class. Reason %s.' % e)
                    try:
                        data += '<img src="http://www.sellcarauction.co.kr'+html.find("div#realImg>div")[i].find("div:last-child>img")[0].attrs["src"]+'" alt="" style="width: 736px; max-width: none">'
                    except Exception as e:
                        print('Can\'t find div:last-child>img src. Reason %s.' % e)
                    for k in range(len(html.find("#realImg>div")[i].find("div:last-child>ul"))):
                        try:
                            html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs
                        except Exception as e:
                            print('Can\'t find div:last-child>ul attrs. Reason %s.' % e)
                        if "class" in html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs:
                            try:
                                data += "<div id='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs["class"][0]+"' class='position-absolute'>"
                            except Exception as e:
                                print('Can\'t find div:last-child>ul class. Reason %s.' % e)
                            for j in range(len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li"))):
                                try:
                                    html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs
                                except Exception as e:
                                    print('Can\'t find div:last-child>ul li attrs. Reason %s.' % e)
                                if "class" in html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs:
                                    if (len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs["class"]) > 0):
                                        try:
                                            data += "<div style='top:0; left:0;' class='position-absolute'>"
                                            data += "<img src='http://www.sellcarauction.co.kr"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("img")[0].attrs["src"]+"'>"
                                            data += "</div>"
                                        except Exception as e:
                                            print('Failed to get li img src. Reason: %s' % e)
                                        try:
                                            data += "<div id='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs["id"]+"' style='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("p")[0].attrs["style"]+"' class='position-absolute ddi'>"
                                        except Exception as e:
                                            print('Failed to get li p style. Reason: %s' % e)
                                        try:
                                            data += "<span class='text-danger'>"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("p>span.txt_red")[0].text+"</span>"
                                        except Exception as e:
                                            print('Failed to get li span text. Reason: %s' % e)
                                        try:
                                            if(len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].xpath("//p/text()")) != 0):
                                                data += html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].xpath("//p/text()")[0]
                                        except Exception as e:
                                            print('Failed to get li out span text. Reason: %s' % e)
                                        data += "</div>"#END position-absolute ddi
                            data += "</div>"#END ID p0101_0107 CLASS position-absolute
                    data += "</div>"#END div class='korea-auc-damage aj type
                    data += '<div class="row mb-2"> <div class="col-12 font-size-sm font-weight-bold">История повреждений</div> <div class="col-lg-4 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold text-danger">X </span> <span class="tbl-caption">Замена</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Была установлена новая деталь и окрашена</div> </div> </div> <div class="col-lg-4 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold text-danger">W </span> <span class="tbl-caption">Ремонт</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Был выполнен ремонт и окраска детали</div> </div> </div> </div>'
                    data += '<div class="row"> <div class="col-12 font-size-sm font-weight-bold">Присутствующие дефекты</div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">A </span> <span class="tbl-caption">Царапина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствуют незначительные царапины</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">C </span> <span class="tbl-caption">Коррозия</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует коррозия</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">T </span> <span class="tbl-caption">Скол</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали есть мелкий скол не требующий ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">TX</span> <span class="tbl-caption">Трещина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Трещина на детали которая требует ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">U </span> <span class="tbl-caption">Вмятина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует мелкая вметинка</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">UR </span> <span class="tbl-caption">Вмятый скол</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует скол с вмятиной не требует ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">H </span> <span class="tbl-caption">Отверстие</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует отверстие, требует ремонта</div> </div> </div> </div>'
                    data += "</div>"#END CLASS p-3
            else:
                data += "<div class='p-3'>"
                try:
                    data += "<div class='korea-auc-damage aj type-"+html.find("div#realImg>div")[i].attrs["class"][0]+" position-relative'>"
                except Exception as e:
                    print('Can\'t div#realImg>div class. Reason %s.' % e)
                try:
                    data += '<img src="http://www.sellcarauction.co.kr'+html.find("div#realImg>div")[i].find("div:last-child>img")[0].attrs["src"]+'" alt="" style="width: 736px; max-width: none">'
                except Exception as e:
                    print('Can\'t find div:last-child>img src. Reason %s.' % e)
                for k in range(len(html.find("#realImg>div")[i].find("div:last-child>ul"))):
                    try:
                        html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs
                    except Exception as e:
                        print('Can\'t find div:last-child>ul attrs. Reason %s.' % e)
                    if "class" in html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs:
                        try:
                            data += "<div id='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].attrs["class"][0]+"' class='position-absolute'>"
                        except Exception as e:
                            print('Can\'t find div:last-child>ul class. Reason %s.' % e)
                        for j in range(len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li"))):
                            try:
                                html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs
                            except Exception as e:
                                print('Can\'t find div:last-child>ul li attrs. Reason %s.' % e)
                            if "class" in html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs:
                                if (len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs["class"]) > 0):
                                    try:
                                        data += "<div style='top:0; left:0;' class='position-absolute'>"
                                        data += "<img src='http://www.sellcarauction.co.kr"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("img")[0].attrs["src"]+"'>"
                                        data += "</div>"#END position-absolute
                                    except Exception as e:
                                        print('Failed to get li img src. Reason: %s' % e)
                                    try:
                                        data += "<div id='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].attrs["id"]+"' style='"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("p")[0].attrs["style"]+"' class='position-absolute ddi'>"
                                    except Exception as e:
                                        print('Failed to get li p style. Reason: %s' % e)
                                    try:
                                        data += "<span class='text-danger'>"+html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].find("p>span.txt_red")[0].text+"</span>"
                                    except Exception as e:
                                        print('Failed to get li span text. Reason: %s' % e)
                                    try:
                                        if(len(html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].xpath("//p/text()")) != 0):    
                                            data += html.find("#realImg>div")[i].find("div:last-child>ul")[k].find("li")[j].xpath("//p/text()")[0]
                                    except Exception as e:
                                        print('Failed to get li out span text. Reason: %s' % e)
                                    data += "</div>"#END position-absolute ddi
                        data += "</div>"#END ID p0101_0107 CLASS position-absolute
                data += "</div>"#END div class='korea-auc-damage aj type
                data += '<div class="row mb-2"> <div class="col-12 font-size-sm font-weight-bold">История повреждений</div> <div class="col-lg-4 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold text-danger">X </span> <span class="tbl-caption">Замена</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Была установлена новая деталь и окрашена</div> </div> </div> <div class="col-lg-4 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold text-danger">W </span> <span class="tbl-caption">Ремонт</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Был выполнен ремонт и окраска детали</div> </div> </div> </div>'
                data += '<div class="row"> <div class="col-12 font-size-sm font-weight-bold">Присутствующие дефекты</div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">A </span> <span class="tbl-caption">Царапина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствуют незначительные царапины</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">C </span> <span class="tbl-caption">Коррозия</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует коррозия</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">T </span> <span class="tbl-caption">Скол</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали есть мелкий скол не требующий ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">TX</span> <span class="tbl-caption">Трещина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">Трещина на детали которая требует ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">U </span> <span class="tbl-caption">Вмятина</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует мелкая вметинка</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">UR </span> <span class="tbl-caption">Вмятый скол</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует скол с вмятиной не требует ремонта</div> </div> </div> <div class="col-lg-3 mb-2"> <div class="tbl-wrap"> <div class="tbl-header font-size-sm"> <span class="tbl-symbol font-weight-bold">H </span> <span class="tbl-caption">Отверстие</span> </div> <div class="tbl-description font-weight-light" style="font-size: .7em">На детали присутствует отверстие, требует ремонта</div> </div> </div> </div>'
                data += "</div>"#END CLASS p-3
    return data

#запись айди авто в файл
def write_car_id(file_name, data):
    if(html != ""):
        try:
            return open(file_name, "a").write(data)
        except Exception as e:
            print('Can\'t wrine file %s. Reason %s.' % (file_name,e))

#ID авто для парсингу
def get_car_id(html):
    if(html !=""):
        try:
            items = html.find("div.car-title")
            print(html.find("div.car-title"))
            for item in items:
                if(len(item.find("a"))):
                    car_id = re.search('[A-Z]+[0-9]+', item.find("a")[0].attrs['onclick'])
                    print(item.find("a")[0])
                    print(car_id)
                    if car_id:
                        write_car_id("car_id.txt",car_id.group()+"\n")
        except Exception as e:
            print('Can\'t get car id. Reason %s.' % e)

#парсимо всі ссилки на авто по сторінкам
def get_pages(page_start=1, page_end=2):
    for i in range(page_start,page_end+1):
        unf = uniform(3,6)
        time.sleep(unf)
        print("Page %d. Sleep %d." % (i, unf))
        html = fetch(url+"?i_iPageSize=&i_iNowPageNo="+str(i))
        get_car_id(html)
    pass
#складаємо всі ссилки на авто в один файл
def get_all_links(car_link, file_name="car_id.txt"):
    return list(map( lambda x: '{}?receivecd={}'.format( car_link, x ), read_file(file_name, "\n")))

#год автомобиля
def get_car_year(html):
    if(html != ''):
        try:
            if(len(html.find("strong.text-right"))>2):
                res = re.search("[0-9]+", str(html.find("strong.text-right")[2].text))
                if(res):
                    year = res.group()
                    if(len(year) == 4):
                        return int(year)
                    else:
                        return int(year[0:4])
        except Exception as e:
            print('Can\'t get car year. Reason %s.' % e)
            return ""
#год первой регистрации автомобиля
def get_car_registration(html):
    if(html != ''):
        try:
            if(len(html.find("strong.text-right"))>2):
                res = re.findall("[0-9]+", rm_new_line(str(html.find("strong.text-right")[2].text)))
                print(res)
                if(len(res)>1):
                    return str(res[1][0:4]+"/"+res[1][4:6]+"/"+res[1][6:8])
                elif(len(res) == 1):
                    return str(res[0][0:4]+"/"+res[0][4:6]+"/"+res[0][6:8])
        except Exception as e:
            print('Can\'t get car registration. Reason %s.' % e)
            return ""
#название авто
def get_car_title(html):
    if(html != ''):
        try:
            if(len(html.find("h2.tit_style2"))):
                return clear_car_name(ko_translate(rm_new_line(str(html.find("h2.tit_style2")[0].text)), "en"))
        except Exception as e:
            print('Can\'t get title. Reason %s.' % e)
            return ""

#категория авто
def get_car_category(html):
    if(html != ''):
        categoty_data = {
                "Genesis":"Genesis",
                "Kia":"Kia Motors",
                "Hyundai":"Hyundai",
                "Ssangyong":"SsangYong",
                "Renault":"Renault",
                "Benz":"Mercedes Benz",
                "Mercedes":"Mercedes Benz",
                "Daewoo":"Chevrolet",
                "Chevrolet":"Chevrolet",
                "Jaguar":"Jaguar",
                "BMW":"BMW",
                "Land":"Land Rover",
                "Peugeot":"Peugeot",
                "Volkswagen":"Volkswagen",
                "Ford":"Ford",
                "Nissan":"Nissan",
                "Jeep":"Jeep",
                "Lexus":"Lexus",
                "Lincoln":"Lincoln",
                "Mini":"Mini Cooper",
                "Cadillac":"Cadillac",
                "Toyota":"Toyota",
                "Tesla":"Tesla",
                "Audi":"Audi",
                "Citroen":"Citroen",
                "Honda":"Honda",
                "Daechang":"Daechang Motors",
                "Chrysler":"Chrysler",
                "Porsche":"Porsche"
            }
        try:
            data = re.match("^[0-9]+\s(\w+)", get_car_title(html))
            if(data):   
                return categoty_data[data.group(1)]
        except Exception as e:
            print('Can\'t get car category. Reason %s.' % e)
            return ""

#цвет автомобиля
def get_car_color(html):
    if(html != ''):
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
            "빨강":"Красный"
        }
        try:
            if(len(html.find("strong.text-right"))>9):
                return color_data[re.sub("\s.+","", rm_new_line(str(html.find("strong.text-right")[9].text)))]
        except Exception as e:
            print('Can\'t get car color. Reason %s.' % e)
            return ""

def get_car_estimate(html):
    if(html != ''):
        try:
            item = html.find("#body > section.con_top.gray-bg_fin > div:nth-child(2) > div > div > div > table > tbody > tr:nth-child(1) > td")[0].text
            if item:
                return "".join(re.findall("[A-Z]", str(item)))
        except Exception as e:
            print('Can\'t get car estimate %s. Reason: %s' % e)

#тип автомобиля
def get_car_type(html):
    if(html != ''):
        car_type_data = {
            "승합 (6인승)":"Универсал",
            "승용 (7인승)":"Универсал",
            "승용 (11인승)":"Фургон",
            "승합 (3인승)":"Фургон",
            "화물 (3인승)":"Фургон",
            "승합 (15인승)":"Фургон",
            "승합 (11인승)":"Фургон",
            "승합 (12인승)":"Фура",
            # "Трактор",
            "승용 (5인승)":"Седан",
            "승합 (5인승)":"Седан",
            # "Родстер",
            # "Пикап",
            # "Мотоцикл",
            "승합 (25인승)":"Автобус",
            "승합 (9인승)":"Минивен",
            "승용 (9인승)":"Минивен",
            "승용 (6인승)":"Минивен",
            "화물 (6인승)":"Минивен",
            "승용 (4인승)":"Хэтчбек",
            # "Кроссовер",
            "화물 (2인승)":"Купе",
            "승용 (2인승)":"Купе",
            "SUV픽업 (5인승)":"Внедорожник пикап"
            # "Кабриолет",
            # "Багги"
        }
        try:
            if(len(html.find("strong.text-right"))>10):
                return car_type_data[rm_new_line(str(html.find("strong.text-right")[10].text))]
        except Exception as e:
            print('Can\'t get car type. Reason %s.' % e)
            return ""

#пробег
def get_distance_driven(html):
    if(html != ''):
        try:
            res = re.findall("[0-9]+", rm_new_line(str(html.find("strong.text-right")[5].text)))
            return "".join(res)
        except Exception as e:
            print('Can\'t get driven distance. Reason %s.' % e)
            return ""

#двигатель
def get_car_displacement(html):
    if(html != ''):
        try:
            res = re.findall("[0-9]+", rm_new_line(html.find("strong.text-right")[6].text))
            return "".join(res)
        except Exception as e:
            print('Can\'t get car displacement. Reason %s.' % e)
            return ""

#коробка передач
def get_transmission(html):
    if(html != ''):
        try:
            if(rm_new_line(str(html.find("strong.text-right")[8].text))=="오토"):
                return "Автомат"
            else:
                return "Механика"
        except Exception as e:
            print('Can\'t get transmission. Reason %s.' % e)
            return ""

#топливо
def get_fuel(html):
    if(html != ''):
        fulel_data = {
            "가솔린":"Бензин",
            "휘발유":"Бензин",
            "경유":"Дизель",
            "디젤":"Дизель",
            "LPG":"LPG",
            "하이브리드":"Гибрид",
            "LPI하이브리드":"LPG гибрид",
            "가솔린하이브리드":"Бензиновый гибрид",
            "디젤하이브리드": "Дизельный гибрид",
            "전기":"Электрокар",
            "가솔린/LPG":"Бензин/LPG",
            "겸용":"Комбинированное использование"
        }
        try:
            if(len(html.find("strong.text-right"))>3):
                return fulel_data[rm_new_line(str(html.find("strong.text-right")[4].text))]
        except Exception as e:
            print('Can\'t get fuel. Reason %s.' % e)
            return ""

#лот аукциона
def get_lot_id(html):
    if(html != ''):
        try:
            if(len(html.find("h2.tit_style2"))):
                res = re.search("\[{1}\d+\]{1}", ko_translate(rm_new_line(str(html.find("h2.tit_style2")[0].text)), "en"))
                if(res):
                    res = res.group()
                    res = re.sub("\[{1}", "", res)
                    res = re.sub("\]{1}", "", res)
                    return res
        except Exception as e:
            print('Can\'t get title. Reason %s.' % e)
            return ""

#марка авто
def get_car_mark(html):
    if(html != ''):
        try:     
            return text_len(rm_new_line(str(html.find("#body > section.con_top.gray-bg_fin > div.container-fluid.wide_area.mt_1.car_view_check_area > div > div > div > table > tbody > tr:nth-child(7) > td:nth-child(2)")[0].text)),"en")
        except Exception as e:
            print('Can\'t get car mark. Reason %s.' % e)
            return ""

#VIN номер авто
def get_car_vin(html):
    if(html != ''):
        try:
            if(len(html.find("#body > section.con_top.gray-bg_fin > div:nth-child(1) > div.row.mt_5 > div.col-md-4.car-details-sidebar > div > ul > li:nth-child(2) > strong"))):
                return str(html.find("#body > section.con_top.gray-bg_fin > div:nth-child(1) > div.row.mt_5 > div.col-md-4.car-details-sidebar > div > ul > li:nth-child(2) > strong")[0].text)
        except Exception as e:
            print('Can\'t get VIN number. Reason %s.' % e)

def get_car_category_url(html):
    if(html != ''):
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
                "Nissan":"nissan",
                "Jeep":"jeep",
                "Lexus":"lexus",
                "Honda":"honda",
                "Lincoln":"lincoln",
                "Mini Cooper":"mini-cooper",
                "Cadillac":"cadillac",
                "Toyota":"toyota",
                "Tesla":"tesla",
                "Audi":"audi",
                "Citroen":"citroen",
                "Daechang Motors":"daechang-motors",
                "Chrysler":"chrysler",
                "Porsche":"porsche"
            }
        try:
            return categoty_url_data[get_car_category(html)]
        except Exception as e:
            print('Can\'t get car category url. Reason %s.' % e)
            return ""

#цена автомобиля 
def get_car_price(html):
    if(html != ''):
        try:
            if(len(html.find("strong.i_comm_main_txt2"))):
                price = int(re.sub("\,", "", html.find("strong.i_comm_main_txt2")[0].text))
                return int(price*10000/int(USD))
        except Exception as e:
            print('Can\'t get car price. Reason %s.' % e)
            return ""

#описание авто 
def get_car_description(html, link):
    description = ''
    try:
        vr = re.search("[A-Z0-9]+$", link)
        if vr:
            vr = vr.group()
    except Exception as e:
        print('Can\'t get car ID for VR 360 view auto. Reason %s.' % e)
    try:
        description += '<div class="timeline-heading"><h3>VR360 обзор авто</h3></div>'
        description += '<iframe frameborder="0" height="600" id="ovulyaciya" scrolling="no" src="http://www.sellcarauction.co.kr/newfront/receive/rc/receive_rc_view_vr.do?isLandscapeOpen=Y&amp;isBrowserOpen=Y&amp;receivecd=%s" width="900"></iframe>' % vr
    except Exception as e:
        print('Can\'t get car description "VR 360 view auto". Reason %s.' % e)
    try:
        description += '<div class="timeline-heading"><h3>Информация об оценке производительности</h3></div>'
        description += text_len(rm_new_line(str(html.find("#body > section.con_top.gray-bg_fin > div:nth-child(2) > div > div > div > table")[0].html)), "ru")
    except Exception as e:
        print('Can\'t get car description "Performance Evaluation Information". Reason %s.' % e)
    try:
        description += '<div class="timeline-heading"><h3>Информация о вариантах</h3></div>'
        description += text_len(rm_new_line(str(html.find("#body > section.con_top.gray-bg_fin > div:nth-child(3) > div > div > table:nth-child(2)")[0].html)), "ru")
    except Exception as e:
        print('Can\'t get car description "Option information". Reason %s.' % e)
    try:   
        description += text_len(rm_new_line(str(html.find("#body > section.con_top.gray-bg_fin > div:nth-child(3) > div > div > ul")[0].html)), "ru")
    except Exception as e:
        print('Can\'t get car descritpion comm list. Reason %s.' % e)
    description += rm_new_line(get_car_img(html))
    # try:
    #     description += '<div class="timeline-heading"><h3>Протокол осмотра</h3></div>'
    #     text = text_len(rm_new_line(str(soup.select("#body > section.con_top.gray-bg_fin > div.container-fluid.wide_area.mt_1.car_view_check_area > div > div > div > table")[0])), "ru")
    #     text = re.sub("\/newfront\/images\/","http://www.sellcarauction.co.kr/newfront/images/",text)
    #     description += text
    # except Exception as e:
    #     print('Can\'t get car description "Inspection protocol". Reason %s.' % e)
    return rm_new_line(description)

#фото авто
def get_img_src(html):
    try:
        items = html.find("img.img-fluid")
        images = []
        for item in items:
            if "src" in item.attrs:
                match = re.search('\_S\.[jpeg|jpg|JPG|JPEG|gif|png]+$', item.attrs['src'])
                if match:
                    images.append(item.attrs['src'].replace('_S', ''))
                else: 
                    continue
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

#записуємо усі данні у файл
def write_csv(data, name = "data.csv"):
    path = os.getcwd() + os.path.sep + name
    try: 
        with open(path, 'a', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow([data['category'], data['category_url'], data['title'], ' ', data['description'], data['price'], ' ', data['images'], data['article'], data['count'], data['activation'], data['title-seo'], ' ', ' ', ' ', data['recomended'], data['new'], ' ', ' ', ' ', ' ', ' ', data['currency'], data['properties']])
    except Exception as e:
        print('Can\'t write csv file. Reason %s' % e)

def rm_csv(name = "data.csv"):
        try:
            os.remove(name)
            print("File "+name+" Removed!")
        except Exception as e:
                print('Failed to remove file %s. Reason: %s' % (name,e))
def create_csv(name = "data.csv"):
    path = os.getcwd() + os.path.sep + name
    try:
        with open(path, 'w', encoding="utf-8", newline='') as file:
            writer = csv.writer(file, delimiter=';')
            writer.writerow(['Категория', 'URL категории', 'Товар', 'Вариант', 'Описание', 'Цена', 'URL', 'Изображение', 'Артикул', 'Количество', 'Активность', 'Заголовок [SEO]', 'Ключевые слова [SEO]', 'Описание [SEO]', 'Старая цена', 'Рекомендуемый', 'Новый', 'Сортировка', 'Вес', 'Связанные артикулы', 'Смежные категории', 'Ссылка на товар', 'Валюта', 'Свойства'])
    except Exception as e:
            print('Failed to create file "data.csv". Reason: %s' % e)

#витягуємо всі данні на авто
def get_car(link):
    # p = current_process()
    # if(p.name != 'MainProcess' and p._identity[0] and os.getpid()):
    #     print('process counter:', p._identity[0], 'pid:', os.getpid())
    # unf = uniform(1,4)
    # time.sleep(unf)
    html = fetch(link)
    print(link)
    car = {}
    year = get_car_year(html)
    category = get_car_category(html)
    if(isinstance(year,int) and (year < 2012)):
        return
    color = get_car_color(html)
    car_estimate = get_car_estimate(html)
    car_type = get_car_type(html)
    distance_driven = get_distance_driven(html)
    displacement = get_car_displacement(html)
    transmission = get_transmission(html)
    fuel = get_fuel(html)
    lot_number = get_lot_id(html)
    car_registration = get_car_registration(html)
    mark = "" if not category and not get_car_mark(html) else category +" "+get_car_mark(html).upper()
    car_vin = get_car_vin(html)
    car['category'] = category
    car['category_url'] = get_car_category_url(html)
    car['title'] = get_car_title(html)
    car['title-seo'] = car['title']
    car['price'] = get_car_price(html)
    car['description'] = get_car_description(html, link)
    car['images'] = get_img_str(get_img_src(html), html)
    car['count'] = '0'
    car['activation'] = '1'
    car['currency'] = 'USD'
    car['recomended'] = '0'
    car['new'] = '0'
    car['article'] = get_lot_id(html)
    car['properties'] = 'Цвет=[type=assortmentCheckBox value=%s product_margin=Желтый|Белый|Серебро|Красный|Фиолетовый|Оранжевый|Зеленый|Серый|Золото|Коричневый|Голубой|Черный|Бежевый]&Кузов=[type=assortmentCheckBox value=%s product_margin=Универсал|Фургон|Фура|Трактор|Седан|Родстер|Пикап|Мотоцикл|Минивен|Хэтчбек|Кроссовер|Купе|Кабриолет|Багги]&Пробег=%s&Двигатель=%s&Год=%s&Дата первой регистрации авто=%s&Трансмиссия=[type=assortmentCheckBox value=%s product_margin=Механика|Автомат]&Топливо=[type=assortmentCheckBox value=%s product_margin=Дизель|Бензин|Газ]&Модель=%s&Марка=%s&Номер лота=%s&Оценка автомобиля=%s&VIN номер=%s&Аукцион=sellcarauction %s' % (color, car_type, distance_driven, displacement, year, car_registration, transmission, fuel, mark, category, lot_number, car_estimate, car_vin, DATE)
    #download_images(get_img_src(html))
    write_csv(car)

#головна функція яка запускається
def main():
    time_start = time.time()

    if(PARSE_CODE == 0):
            rm_csv("car_id.txt")
            get_pages(1,MAX_PAGE)
    else:
        rm_csv()
        create_csv()
        my_list = get_all_links(car_link)
        with Pool(15) as p:
            p.map(get_car, my_list) 

    time_end = time.time() 
    print(time_end - time_start)

if __name__ == '__main__':
    main()