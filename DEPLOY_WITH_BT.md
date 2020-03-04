# 通过宝塔运维面板进行部署

感谢您关注我们的项目！本文档主要内容为：如何通过宝塔运维面板进行后端程序的快速部署。

- [宝塔简介](#btjj)
- [环境准备](#hjzb)
- [面板安装](#mbaz)
- [环境配置](#hjpz)
- [参数设置](#cssz)

## <a name="btjj"></a>宝塔简介

- 简介：宝塔面板是一款服务器管理软件，支持windows和linux系统，可以通过Web端轻松管理服务器，提升运维效率。例如：创建管理网站、FTP、数据库，拥有可视化文件管理器，可视化软件管理器，可视化CPU、内存、流量监控图表，计划任务等功能。

- 功能：宝塔面板拥有极速方便的一键配置与管理，可一键配置服务器环境（LAMP/LNMP/Tomcat/Node.js），一键部署SSL，异地备份；提供SSH开启关闭服务，SSH端口更改，禁ping，防火墙端口放行以及操作日志查看；CPU、内存、磁盘IO、网络IO数据监测，可设置记录保存天数以及任意查看某天数据；计划任务可按周期添加执行，支持SHELL脚本，提供网站、数据库备份以及日志切割，且支持一键备份到又拍云存储空间，或者其他云存储空间里；通过web界面就可以轻松管理安装所用的服务器软件，还有实用的扩展插件；集成方便高效的文件管理器，支持上传、下载、打包、解压以及文件编辑查看。

- 域名：https://www.bt.cn/

## <a name="hjzb"></a>环境准备

- 服务器，内存：512M以上，推荐768M以上（纯面板约占系统60M内存），硬盘：100M以上可用硬盘空间（纯面板约占20M磁盘空间）

- 系统：CentOS 7.1+ (Ubuntu16.04+.、Debian9.0+)，**确保是干净的操作系统**，没有安装过其它环境带的Apache/Nginx/php/MySQL，**已有环境不可安装**

- 打开服务器端口：**以下主机商必看**

- 腾讯云：https://www.bt.cn/bbs/thread-1229-1-1.html 

- 阿里云：https://www.bt.cn/bbs/thread-2897-1-1.html 

- 华为云：https://www.bt.cn/bbs/thread-3923-1-1.html

## <a name="mbaz"></a>宝塔面板安装命令

在相应服务器终端环境下执行：

- Centos安装命令：yum install -y wget && wget -O install.sh [http://download.bt.cn/install/install_6.0.sh](http://download.bt.cn/install/install_6.0.sh) && sh install.sh

- Ubuntu/Deepin安装命令：wget -O install.sh http://download.bt.cn/install/install-ubuntu_6.0.sh && sudo bash install.sh

- Debian安装命令：wget -O install.sh http://download.bt.cn/install/install-ubuntu_6.0.sh && bash install.sh

- Fedora安装命令：wget -O install.sh http://download.bt.cn/install/install_6.0.sh && bash install.sh

## <a name="hjpz"></a>宝塔环境配置

- 安装完毕后，使用生成的URL和账户密码就可以进入宝塔面板

- 安装套件：一键安装LNMP

- 在软件商店中搜索并安装：宝塔一键部署源码1.1

- 在宝塔一键部署源码1.1中搜索并安装：ThinkPHP-5.0

- 将已申请好的域名填入

- 记录下生成的数据库名、用户名、密码

- 打开面板左侧的 **文件** 标签将本项目中 ncov-report-mini-program-server/src下的文件上传至
  服务器中，路径为：根目录/www/wwwroot/您的项目名/，与服务器中原有文件夹名相对应即可。

## <a name="cssz"></a>数据导入与参数设置

- 在面板左侧 **数据库** 中可导入文件，数据文件请移步：https://github.com/2020NCOV/ncov-report

- 根据自身情况修改以下文件：（若已修改，请忽略）

  applicaiton/config.php文件

  ```
      //修改以下内容为实际数据
      'wechat_appid'    => '小程序ID',
      'wechat_secret'		=> '秘钥',
  ```

  applicaiton/database.php文件

  ```
      //修改以下内容为实际数据
      // 数据库类型
      'type'            => 'mysql',
      // 服务器地址
      'hostname'        => '127.0.0.1',
      // 数据库名
      'database'        => '数据库名',
      // 用户名
      'username'        => '用户名',
      // 密码
      'password'        => '密码',
  ```

  

至此，小程序后端部分部署完成