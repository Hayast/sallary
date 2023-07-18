//export const MQTT_IP = '47.115.19.189:2883/mqtt'//mqtt地址端口
export const MQTT_IP = 'n.yunitapp.com/mqtt'//mqtt地址端口 用域名不加端口 
const MQTT_USERNAME = 'public'//mqtt用户名
const MQTT_PASSWORD = 'public'//密码
 
export const MQTT_OPTIONS = {
    connectTimeout: 5000,
    clientId: '',
    username: MQTT_USERNAME,
    password: MQTT_PASSWORD,
    clean: false
}