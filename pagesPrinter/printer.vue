<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<span>打印机设置</span>
				<u-icon name="search" color="#2979ff" size="36" @click="isShowSearch=!isShowSearch"></u-icon>
			</view>
		</view>
		<view class="body page-box">
			<view class="s-v text-center">
				打印机: {{globalInfo.DefaultBlePrinter.name || '未设置打印机'}} 
			</view>
			<view class="row-l" >
				<u-switch color="red" v-model="isLabel" @change="switchB" />
				{{PrintTypeText[globalInfo.PrinterType]}}
			</view>
			<view class="s-v">
				<view v-if="globalInfo.CurrPrinterInfo.isConnected">
					<u-button type="primary" @click="labelTest()">测试打印</u-button>
				</view>
			</view>
			
			<view class="s-v">
				<view v-if="globalInfo.DefaultBlePrinter.name!=''">
					<u-button type="primary" @click="initBleConn()">连接打印机</u-button>
				</view>
			</view>
			

		</view>
		<view class="kk-printer">
			<view class="kk-shadow" :class="isShowSearch?'show':''" @tap="stopFindBule">
				<view class="kk-modal" @tap.stop="doNothing">
					<view class="kk-search-device">
						<view style="margin-bottom: 15px;">
							<u-row>
								<u-col :span="11">搜索连接蓝牙设备</u-col>
								<u-col :span="1">
									<u-icon name="close-circle" color="#2979ff" size="36" @click="isShowSearch=false"></u-icon>
								</u-col>
							</u-row>
						</view>
						<view v-if="false" class="kk-filter-wrap">
							<view class="filter-title">根据SRRI过滤设备</view>
							<slider max='-20' min='-100' value="-95" show-value />
						</view>
						<view v-if="false" class="kk-filter-wrap">
							<view class="filter-title">根据蓝牙名过滤</view>
							<input type="text" placeholder-class="kk-placeholder-class" placeholder="请输入蓝牙名字或设备ID搜索"
								v-model="filterName" />
						</view>
						<view class="kk-btn-wrap">
							<view class="kk-btn-item confirm-btn" @tap="searchBle" v-if="!globalInfo.IsSearching">
								搜索设备
							</view>
							<view class="kk-btn-item confirm-btn" v-else>
								搜索中...
							</view>
							<view class="kk-btn-item" @tap="stopFindBule">
								停止搜索
							</view>
						</view>
						<view class="kk-devices-wrap">
							<view class="empty-wrap" v-if="globalInfo.BleDevice.length <= 0">
								<view class="empty-icon"></view>
								<view class="empty-text" @tap="isShowSearch=false">~ 无可搜索到的设备 ~</view>
							</view>
							<view class="" v-else>
								<view class="kk-devices-item" v-for="(item,index) in globalInfo.BleDevice" :key="index"
									@tap="onConn(item)">
									<view class="name">
										<text>设备名称：</text>
										<text>{{item.name?item.name:'未命名'}}</text>
									</view>
									<view class="rssi">
										<text>信号强度：</text>
										<text>({{Math.max(0, item.RSSI + 100)}}%)</text>
									</view>
									<view class="deviceid">
										<text>设备ID：</text>
										<text>{{item.deviceId}}</text>
									</view>
									<view class="advmac" v-if="item.advMac">
										<text>advMac：</text>
										<text>{{item.advMac}}</text>
									</view>
								</view>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		
	</view>
</template>

<script>
	import Vue from 'vue';
	import escPrinter from '@/pagesPrinter/components/gprint/esc.js'
	import tscPrinter from '@/pagesPrinter/components/gprint/tsc.js'
	Vue.prototype.escPrinter = escPrinter
	Vue.prototype.tscPrinter = tscPrinter
	
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	export default {
		data() {
			return {
				PrintTypeText: ['票据打印机','标签打印机',''],
				devices: [],
				//是否显示蓝牙列表
				isLabel: true,
				isShowSearch: false,
				IsSearching: false,
				//按蓝牙名过滤
				filterName: '',
				//按信号过滤
				filterRSSI: -95,
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad: function() {
			if (this.globalInfo.PrinterType == 0) {
				this.isLabel=false;
			}
			this.initPrinter();
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			async initPrinter(){
				console.log(this.globalInfo.DefaultBlePrinter)
				if (this.globalInfo.DefaultBlePrinter.name!='' && this.globalInfo.DefaultBlePrinter.name!=undefined && !this.globalInfo.CurrPrinterInfo.isConnected) {
					await this.BlePrinter.initBleConn();
				}
			},
			doNothing(){
				return;
			},
			async initBleConn(){
				await this.BlePrinter.initBleConn();
			},
			async stopFindBule(){
				await this.BlePrinter.stopFindBule();
				//this.isShowSearch=false;
			},
			switchB(e) {
				if (e) {
					this.setGlobalInfo({
						name: "PrinterType",
						value: 1
					})
				} else {
					this.setGlobalInfo({
						name: "PrinterType",
						value: 0
					})
				}
			},
			async searchBle() {
				await this.BlePrinter.searchBle();
			},
			async onConn(item) {
				await this.BlePrinter.onConn(item);
				this.isShowSearch=false;
			},
			async labelTest() {
				var _PrintType = this.globalInfo.PrinterType
				var printData
				if (_PrintType == '1') {
					//标签打印
					let command = this.tscPrinter.jpPrinter.createNew();
					command.setSize(50, 70);
					command.setGap(2);
					command.setCls();
					command.setQR(120, 50, 'L', 8, 'A', '13580704353');
					command.setText(20, 300, 'TSS24.BF2', 1, 1, '联络手机: 13580704353');
					command.setText(20, 340, 'TSS24.BF2', 1, 1, '扫码系统');
					command.setPagePrint();
					printData=command.getData();
					//this.senBlData(command.getData());
				} else{
					//票据打印
					var command = this.escPrinter.jpPrinter.createNew();
					command.setText('联络手机: 13580704353');
					command.setPrintAndFeedRow(1);
					command.setText('名称: 票据打印');
					command.setPrintAndFeedRow(1);
					command.setText('扫码系统');
					command.setPrintAndFeedRow(1);
					command.setText('数量: 1500件');
					command.setPrintAndFeedRow(1);
					printData=command.getData();
				}
				
				await this.BlePrinter.senBleData(printData);
			}

		}
	}
</script>


<style lang="scss" scoped>
	.s-v{
		padding: 15px;
	}
	
	.s-v-u-s{
		padding: 15px;
		display: flex;
		flex-flow: row nowrap;
		//justify-content: space-between;
	}
	
	.kk-printer {
		&-btn {
			width: 100%;
			height: 100%;
		}

		.kk-shadow {
			display: none;

			&.show {
				display: block;
				width: 100vw;
				height: 100vh;
				background: rgba(0, 0, 0, 0.4);
				position: fixed;
				top: 0;
				left: 0;
				display: flex;
				justify-content: center;
				align-items: center;

				.kk-modal {
					width: 680upx;
					height: 80%;
					padding: 24upx;
					box-sizing: border-box;
					overflow-y: auto;
					border-radius: 20upx;
					background: #ffffff;
					display: flex;
					justify-content: center;
					align-items: center;

					.kk-search-device {
						width: 100%;
						height: 100%;

						.kk-filter-wrap {
							width: 100%;
							height: 160upx;
							display: flex;
							flex-direction: column;
							justify-content: flex-start;
							align-items: flex-start;

							.filter-title {
								line-height: 70upx;
								font-size: 30upx;
								color: #999999;
							}

							&>slider {
								width: 90%;
								height: 90upx;
							}

							&>input {
								padding: 0 20upx;
								box-sizing: border-box;
								border-radius: 10upx;
								height: 90upx;
								width: 100%;
								border: 1upx solid #ebebeb;
							}
						}

						.kk-btn-wrap {
							width: 100%;
							height: 140upx;
							display: flex;
							justify-content: space-between;
							align-items: center;

							&>view {
								flex: 1 1 auto;
								height: 100upx;
								line-height: 100upx;
								border-radius: 16upx;
								text-align: center;
								color: #ffffff;

								&.confirm-btn {
									background: #007AFF;
									margin-right: 30upx;
								}

								&:nth-last-child(1) {
									background: #DD524D;
								}
							}
						}

						.kk-devices-wrap {
							height: calc(100% - 260upx);
							overflow-y: auto;
							padding: 10upx 20upx;
							box-sizing: border-box;
							border: 1upx solid #ebebeb;
							box-sizing: border-box;
							border-radius: 20upx;

							.empty-wrap {
								width: 100%;
								height: 100%;
								display: flex;
								flex-direction: column;
								justify-content: center;
								align-items: center;

								.empty-icon {
									width: 268upx;
									height: 240upx;
									background-size: 100% 100%;
									margin-bottom: 26upx;
								}

								.empty-text {
									width: 100%;
									line-height: 50upx;
									font-size: 30upx;
									text-align: center;
									color: #999999;
								}
							}

							.kk-devices-item {
								width: 100%;
								border-bottom: 1upx solid #ebebeb;
								padding: 10upx 0;
								box-sizing: border-box;

								&:nth-last-child(1) {
									border-bottom: none;
								}

								&>view {
									width: 100%;
									font-size: 30upx;
								}
							}
						}
					}
				}
			}

		}
	}

	.kk-placeholder-class {
		font-size: 30upx;
		color: #999999;
	}
</style>
