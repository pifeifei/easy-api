# easy api client



流程图：

```text

config
    https://gitee.com/pifeifei/easy-api

request 
    uri : http://host:port/prefix_path
    method: get, post, json, xml (后面2中都会转换成 post 发送)
    // headers
    query: ? 后面的参数
    post： 参数
    // file: 发送文件，比如批量操作可能需要
    guzzle client options

response
    status code
    headers
    body

result
    
guzzleClientOptions


哪些会修改
    method
    query
    post
    url/path
    
    签名计算，设置

    query,post 参数处理: 签名、排序，转json，转 xml 等


client 负责存数据， 对外暴露

request 负责 处理数据 & 请求，建议只在内部使用

```

