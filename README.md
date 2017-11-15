# Smart-Traffic
## 绍兴市城区智慧交通可视化平台

闲暇之余发现了绍兴交通官方APP的应用程序，于是就拿来改了一下做了个B/S架构的可视化智慧交通云平台。

此程序基于PHP+MySQL构建，已在LAMP、LNMP、WIMP下测试兼容通过。

本系统目前支持以下特性：
- 1、主城区所有主要道路通行状况的实时数据显示（面板10秒自动刷新数据）
- 2、所有行政片区、热门街区的路况实时数据
- 3、自动评估全城总体路况
- 4、计算片区平均TPI拥堵指数
- 5、支持大数据分析（尚未完善，后期预计通过使用Python实现）
- 6、用户登录/注册，并支持与耶鲁大学CAS系统对接（通过phpCAS客户端类库实现，请预先部署JavaCAS服务端）

本系统仍然处于开发过程，如有Bug或建议，欢迎通过Issues反馈。

## &copy; 2012-2017 <a href="http://www.dingstudio.cn" target="_blank">DingStudio</a> Technology All Rights Reserved (Author: <a href="http://954759397.qzone.qq.com" target="_blank">David Ding [alone◎浅忆]</a>)
