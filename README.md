# KanColle-APIs
一些舰C玩家可能有用的中文API接口（PHP语言）

## 使用组件
[TwitterAPIExchange](https://github.com/J7mbo/twitter-api-php) [MIT Lisence](https://github.com/J7mbo/twitter-api-php/raw/master/LICENSE.md)

## 简要说明
此API是闲着没事摸鱼时候糊的，所以部分地方可能看起来不是那么符合API标准(比如有些地方嫌写JSON麻烦，直接返回了文字之类- -)，请各位dalao别笑话我Orz

### KanColleTwitter.php
主要是获取官方Twitter的内容，改改也可以拿去拉其它的Twitter。

#### 使用前需要
1.修改PHP中$settings的access_token，获取token方法请前往[Twitter开发者页面](https://developer.twitter.com)。

```php
$settings = array( //To-do
	'oauth_access_token' => "23333",
	'oauth_access_token_secret' => "23333",
	'consumer_key' => "23333",
	'consumer_secret' => "23333"
);
```

2.修改TwitterAPI.php中performRequest函数$options内CURLOPT_PROXY与CURLOPT_PROXYPORT两项，将其改为自己的HTTP代理服务器，否则无法访问Twitter(海外服务器可将这两行删除)。

```php
$options = $curlOptions + array(
    CURLOPT_HTTPHEADER => $header,
    CURLOPT_HEADER => false,
    CURLOPT_URL => $this->url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
	CURLOPT_PROXY => "127.0.0.1", //To-do: Edit proxy server
	CURLOPT_PROXYPORT => 8088, //To-do: Edit port
	CURLOPT_SSL_VERIFYPEER => false
);
```

#### 参数列表

输入参数：

```
username: Twitter用户名，如KanColle_STAFF
type: 若为1，则获取其头像，否则获取其第一条Tweet
```

输出参数：(JSON格式)

```
text: Tweet内容文字(完整版,非摘要)
link: 该Tweet的链接
id: 该Tweet的ID
image: 该Twitter的头像原图URL
```

### KanColleEvent.php
一个从[KCWIKI](https://zh.kcwiki.org/wiki/)爬取活动信息(航程图、黑板翻译、简易攻略、通关奖励)的小接口。

输入参数：

```
mission: 关卡，即E-X，请求信息用
type: 请求内容，详细参数见PHP中switch
```

输出: 比较懒直接输出文字了Orz