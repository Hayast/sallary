<template>
	<view class="websockets">
		<button type="primary" @tap="clickRequest">点击发送请求</button>
		<button type="primary" @tap="leave">离开页面</button>
		<view>
			信息:{{msg}}
		</view>
	</view>
</template>
 
<script>
	export default {
		onLoad() {
			// 进入这个页面的时候创建websocket连接【整个页面随时使用】
			this.connectSocketInit();
		},
		data() {
			return {
				msg:'',
				socketTask: null,
				// 确保websocket是打开状态
				is_open_socket: false
			}
		},
		// 关闭websocket【必须在实例销毁之前关闭,否则会是underfined错误】
		beforeDestroy() {
			this.closeSocket();
		},
		methods: {
			// 进入这个页面的时候创建websocket连接【整个页面随时使用】
			connectSocketInit() {
				let that=this;
				// 创建一个this.socketTask对象【发送、接收、关闭socket都由这个对象操作】
				this.socketTask = uni.connectSocket({
					// 【非常重要】必须确保你的服务器是成功的,如果是手机测试千万别使用ws://127.0.0.1:9099【特别容易犯的错误】
					url: "ws://b8.xinbda.com/socket",
					success(data) {
						that.msg='websocket连接成功';
						console.log("websocket连接成功");
					},
					fail(err){
						that.msg=err;
					}
				});
 
				// 消息的发送和接收必须在正常连接打开中,才能发送或接收【否则会失败】
				this.socketTask.onOpen((res) => {
					console.log("WebSocket连接正常打开中...！");
					this.is_open_socket = true;
					// 注：只有连接正常打开中 ，才能正常成功发送消息
					this.socketTask.send({
						data: "uni-app发送一条消息",
						async success() {
							that.msg='消息发送成功';
							console.log("消息发送成功");
						},
					});
					// 注：只有连接正常打开中 ，才能正常收到消息
					this.socketTask.onMessage((res) => {
						that.msg='收到服务器内容';
						console.log("收到服务器内容：" + res.data);
					});
				})
				// 这里仅是事件监听【如果socket关闭了会执行】
				this.socketTask.onClose(() => {
					that.msg='已经被关闭了';
					console.log("已经被关闭了")
				})
			},
			// 关闭websocket【离开这个页面的时候执行关闭】
			closeSocket() {
				let that=this;
				this.socketTask.close({
					success(res) {
						this.is_open_socket = false;
						this.msg='关闭成功';
						console.log("关闭成功", res)
					},
					fail(err) {
						this.msg='关闭成功';
						console.log("关闭失败", err)
					}
				})
			},
			clickRequest() {
				let that=this;
				if (this.is_open_socket) {
					// websocket的服务器的原理是:发送一次消息,同时返回一组数据【否则服务器会进去死循环崩溃】
					this.socketTask.send({
						data: "请求一次发送一次message",
						async success() {
							that.msg='消息发送成功';
							console.log("消息发送成功");
						},
					});
				}
			},
			leave() {
				//this.$uniReLaunch("/pages/tabbar/wallet/wallet")
			}
		}
	}
</script>