<template>
	<view>
		<u-navbar leftIconColor="#FFFFFF" bgColor="#3c9cff" @leftClick="handleLeftClick">
			<view slot="center">
				<text style="color: #FFFFFF;">聊天界面</text>
			</view>
		</u-navbar>
		<view style="width: 100%;background-color: #000000;">
			<scroll-view :style="{height: height+'px'}" scroll-y>
			</scroll-view>
		</view>
		<view class="footer" ref="footer" id="footer">
			<view class="content-wrap">
				<textarea class="content" v-model="text" maxlength="-1" auto-height />
			</view>
			<view class="btn-wrap">
				<button class="btn" :disabled="disable" :class="{'disabled': disable}" @click="handleSend">发送</button>
			</view>
		</view>
	</view>
</template>

<script>
	import { v4 } from 'uuid';
	import {
		MQTT_IP,
		MQTT_OPTIONS
	} from '@/util/mqtt.js';
	var mqtt = require('mqtt/dist/mqtt.js')
	var client
	
	export default {
		data() {
			return {
				topic: 'test' //要订阅的主题
			}
		},
		mounted() {
			this.connect() //连接
		},
		methods: {
			connect() {
				MQTT_OPTIONS.clientId = v4()
				var that = this
				// #ifdef H5
				client = mqtt.connect('ws://' + MQTT_IP, MQTT_OPTIONS)
				// #endif
				// #ifdef MP-WEIXIN||APP-PLUS
				client = mqtt.connect('wx://' + MQTT_IP, MQTT_OPTIONS)
				// #endif
				client.on('connect', function() {
					console.log('连接成功')
					client.subscribe(that.topic, function(err) {
						if (!err) {
							console.log('订阅成功')
						}
					})
				}).on('reconnect', function(error) {
					console.log('正在重连...', that.topic)
				}).on('error', function(error) {
					console.log('连接失败...', error)
				}).on('end', function() {
					console.log('连接断开')
				}).on('message', function(topic, message) {
					console.log('接收推送信息：', message.toString())
				})
			}
		}
	}
</script>
<style lang="scss">
	.footer {
		width: 100%;
		background-color: #E9EDF4;
		display: flex;
		position: fixed;
		bottom: 0;
	}

	.footer .content-wrap {
		width: 78%;
		margin-left: 2%;
	}

	.footer .content {
		width: 100%;
		box-sizing: border-box;
		margin: 14rpx 0;
		background-color: #FFFFFF;
		border-radius: 30rpx;
		padding: 16rpx;
		caret-color: #01B4FE;
	}

	.footer .btn-wrap {
		width: 18%;
		margin-right: 2%;
	}

	.footer .btn {
		width: 15%;
		height: 65rpx;
		font-size: 26rpx;
		margin-left: 2%;
		background-color: #01B4FE;
		color: #FFFFFF;
		position: fixed;
		bottom: 14rpx;
		border: 0;
		outline: none;
	}

	.footer .btn-wrap .disabled {
		background-color: #aae8f5;
	}

	/deep/ .uni-textarea-wrapper {
		max-height: 180rpx;
	}
</style>


<style>

</style>
