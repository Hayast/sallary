/**
 * 响应拦截
 * @param {Object} http 
 */
module.exports = (vm) => {
    uni.$u.http.interceptors.response.use((response) => { /* 对响应成功做点什么 可使用async await 做异步操作*/
        const data = response.data
		console.log('=============Util=>interceptors服务端返回状态码response.statusCode!=200，则reject() ================',response)
        // 自定义参数
        const custom = response.config?.custom
		const statusCode=response.statusCode
		console.log('---------------',statusCode)
        if (statusCode !== 200) { // 服务端返回的状态码不等于200，则reject()
            // 如果没有显式定义custom的toast参数为false的话，默认对报错进行toast弹出提示
            if (custom.toast !== false) {
                uni.$u.toast(data.msg)
            }
            // 如果需要catch返回，则进行reject
            if (custom?.catch) {
                return Promise.reject(data)
            } else {
                // 否则返回一个pending中的promise
                return new Promise(() => { })
            }
        }
        return data || {}
    }, (response) => { /*  对响应错误做点什么 （statusCode !== 200）*/
		console.log('********response*********',response.statusCode,response.data)
		if(response.statusCode!=200){
			if(response.statusCode==401){
				uni.$u.toast('请重新登陆.')
				setTimeout(()=>
					uni.reLaunch({
						url:'/pagesUcenter/login'
					})
				,20);
			}else{
				console.log(response.data.message)
			}
			return;
		}
        return Promise.reject(response)
    })
}