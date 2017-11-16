import os
import sys
import http.client
import json
import codecs
import time
import datetime
import sqlite3


sqlconn = sqlite3.connect('test.db')
c = sqlconn.cursor()
result = c.execute('select * from speed')
for row in result:
    print("ID = " + row[0])
    print("name = " + row[1])
    print("speed = " + row[2])
    print("tpi = " + row[3])
    print("message = " + row[4])
    print("\n\n")

sqlconn.close()

