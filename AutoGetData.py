import os
import sys
import http.client
import json
import codecs
import time
import datetime
import sqlite3


    

def getData():
    conn = http.client.HTTPConnection("220.191.224.89")
    conn.request("GET", "/wx/data.php?t=dlzs")
    jsonstr = conn.getresponse()
    return jsonstr

def initDB():
    print("数据库初始化程序")
    sqlconn = sqlite3.connect('test.db')
    c = sqlconn.cursor()
    c.execute('CREATE TABLE speed (\
                id int auto_increment primary key,\
                name text not null,\
                speed int not null,\
                tpi int not null,\
                intro text not null)')
    print("\n数据库表创建并初始化完毕！")

def Main():
    if (getData().status != 200):
        print("远程服务器连接失败，请检查网络状态。")
        exit()
    print("成功连接服务器，以下为HTTP响应报文：\n" + str(getData().msg) + "\n\n")
    if (input("开始抓取数据请输入[start]：") == "start"):
        #print(getData().read())
        data = json.loads(getData().read())
        print("共有" + str(len(data)) + "条数据。")
        reqtime = time.strftime("%Y%m%d%H%M%S",time.localtime(time.time()))
        sqlconn = sqlite3.connect('test.db')
        c = sqlconn.cursor()
        fullspeed = 0
        for newdata in data:
            #print(newdata["speed"])
            c.execute('INSERT INTO speed (name,speed,tpi,intro) VALUES ("' + newdata["name"] + '","' + str(newdata["speed"]) + '","' + str(newdata["tpi"]) + '","' + newdata["grade"] + '")')
            fullspeed = fullspeed + newdata["speed"]
        print("平均速度：" + str(int(fullspeed / len(data))) + " Km/h")
        input()
        exit()
    else:
        print("\n用户已取消操作！按任意键退出。");
        input()
        exit()


first_warn = input("是否初始化数据库表？如果您已有数据，请不要执行此操作。输入y继续，n取消！")
if (first_warn == "y"):
    initDB()
elif (first_warn == "n"):
    Main()
else:
    exit()
