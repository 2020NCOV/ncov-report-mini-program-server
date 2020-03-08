# 2020NCOV-小程序服务端程序介绍 
[![image](https://img.shields.io/badge/</>-Thinkphp-blue.svg)](https://github.com/top-think/think)
[![image](https://img.shields.io/badge/license-Apache2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)
- [项目详细介绍](#项目详细介绍)
- [项目框架](#项目框架)
- [环境说明](#环境说明)
- [配置说明](#配置说明)
- [主要功能](#主要功能)
- [如何部署](#如何部署)
  - [使用Docker部署](#使用docker部署)
  - [使用宝塔部署](#使用宝塔部署)
- [交流方式](#交流方式)
- [在线Demo](#在线demo)
- [贡献指南](#贡献指南)
- [共享者指南](#共享者指南)
- [License](#license)
## 项目详细介绍
[2020NCOV-小程序服务端程序](https://github.com/2020NCOV/ncov-report-mini-program-server)与[2020NCOV-小程序端程序](https://github.com/2020NCOV/ncov-report-mini-program)所配套部署，形成一个基于微信小程序来进行疫情上报和人员健康管理的平台，旨在帮助各高校及企事业单位，在自己的服务器上本地部署一套人员健康管理系统，以满足机构的数据安全策略。
## 项目框架
小程序后端使用[Thinkphp5.0框架](https://github.com/top-think/think)进行编写。  
时序图见[TimingDiagram](https://github.com/2020NCOV/ncov-report-mini-program-server/blob/master/TimingDiagram.md)。
## 环境说明
- `PHP`版本不低于`PHP5.4`  
- 项目运行需支持`PATHINFO`  
- `Apache`：已在项目根目录加入`.htaccess`文件，只需开启`rewrite`模块  
## 配置说明
1. 导入数据库文件`db.sql`，数据库文件见[这里](https://github.com/2020NCOV/ncov-report/tree/master/database)。  

2. 配置`application/config.php`文件  
 ```
'wechat_appid' => getenv('WECHAT_APPID')?getenv('WECHAT_APPID'):'your AppID',
'wechat_secret' => getenv('WECHAT_SECRET')?getenv('WECHAT_SECRET'):'your AppSecret',
```
3. 配置`application/database.php`文件  
```
// 数据库类型  
    'type' => 'mysql',  
    // 服务器地址  
    'hostname' => getenv('DB_HOST')?getenv('DB_HOST'):'127.0.0.1',  
    // 数据库名  
    'database' => getenv('DB_DB')?getenv('DB_DB'):'your database name',  
    // 用户名  
    'username' => getenv('DB_USER')?getenv('DB_USER'):'your username',  
    // 密码  
    'password' => getenv('DB_PASS')?getenv('DB_PASS'):'your password',  
    // 端口  
    'hostport' => getenv('DB_PORT')?getenv('DB_PORT'):'',
```
## 主要功能
- 每日上报
- 个人健康码
- 人员管理
- 上报统计
- 数据下载
- 预警信息
## 如何部署
目前提供两种部署方式：
### 使用Docker部署
2020NCOV-小程序服务端程序可通过Docker进行部署，具体部署指南详见[使用Docker部署]()。
### 使用宝塔部署
2020NCOV-小程序服务端程序可通过宝塔进行部署，具体部署指南详见[DEPLOY_WITH_BT](https://github.com/2020NCOV/ncov-report-mini-program-server/blob/master/DEPLOY_WITH_BT.md)。
## 交流方式
点击加入[钉钉群组]()。  
线上交流，后期课程以及相关资源将会在钉钉群组进行发布，请及时加入并关注信息更新。
## 在线Demo  

## 贡献指南
欢迎贡献您的代码或者参与讨论！
在此之前请您阅读我们的[贡献指南](https://github.com/2020NCOV/ncov-report-mini-program-server/blob/master/CONTRIBUTING_CN.md)。
## 共享者指南
如果您希望使用我们的项目，请在钉钉群组中与项目的核心成员取得联系，我们会尽快对于您的咨询进行回复。
## License
[Apache 2.0 License.](https://opensource.org/licenses/Apache-2.0)
