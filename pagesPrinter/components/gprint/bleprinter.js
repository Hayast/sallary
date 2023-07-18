import {
	mapState,
	mapGetters,
	mapMutations
} from 'vuex'
import global from '@/store/modules/global.js'
import store from '@/store'
var devices = [];
var count=1;

let BlePrinter = {
	...mapMutations({
		setUserInfo: 'user/login',
		setGlobalInfo: 'global/set',
	}),
	
	uniAsyncPromise(name, options) {
		return new Promise((resolve, reject) => {
			uni[name]({
				...(options || {}),
				// ...options,
				success: (res) => {
					resolve(res);
				},
				fail: (err) => {
					reject(err);
				}
			});
		});
	},
	destroyed() {
		let deviceId=global.state.DefaultBlePrinter.deviceId;
		console.log('destroyed-----deviceId: '+deviceId);
		if (deviceId) {
			uni.closeBluetoothAdapter({
				deviceId: deviceId,
				success(res) {
					var printInfo={
							isConnected:false,
							deviceId:'',
							serviceId:'',
							characteristicId:''
						}
					store.commit('global/set', {name:"CurrPrinterInfo",value:printInfo})
					console.log('断开蓝牙--------' + res);
				}
			});
		}
	},
	searchBle(deviceId='') {
		console.log('--------------------searchBle-----------------------------')
		var that = this;
		uni.openBluetoothAdapter({ //初始化蓝牙模块
			success(res) {
				console.log('打开 蓝牙模块');
				console.log(res);
				uni.getBluetoothAdapterState({ //获取本机蓝牙适配器
					success: function(res) {
						console.log(res);
						if (res.available) {
							if (res.discovering) {
								that.stopFindBule();
							}
							//开始搜寻附近的蓝牙外围设备
							console.log('开始搜寻附近的蓝牙外围设备');
							uni.startBluetoothDevicesDiscovery({
								allowDuplicatesKey: false,
								success(res) {
									that.onDevice(deviceId);
									console.log(res);
								}
							});
						} else {
							uni.showToast({
								title: '本机蓝牙不可用',
								icon: 'none'
							})
						}
					}
				});
			},
			fail(err) {
				uni.showToast({
					title: '本机蓝牙未开启或不能使用！',
					icon: 'none'
				})
				console.log(err)
			}
		});
	},
	onDevice(defaultDeviceId='') {
		var that = this;
		store.commit('global/set', {name:"IsSearching",value:true})
		uni.onBluetoothDeviceFound(function(devices) {
			console.log('------new------ ' + JSON.stringify(devices));
			var re = JSON.parse(JSON.stringify(devices));
			// console.log(re.devices[0].name + '  ' + re.devices[0].deviceId);
			let name = re.devices[0].name;
			let deviceId = re.devices[0].deviceId;
			let RSSI = re.devices[0].RSSI;
			console.log('devices', that.devices, 'name', name)
			
			if (that.devices == undefined)
				that.devices = []
			if (that.devices.length == 0) {
				if (name.length > 4) {
					that.devices.push({
						name: name,
						deviceId: deviceId,
						services: [],
						RSSI: RSSI
					});
					store.commit('global/set', {name:"BleDevice",value:JSON.parse(JSON.stringify(that.devices))})
				}
			} else {
				if (name != '') {
					for (var i = 0; i < that.devices.length; i++) {
						if (that.devices[i].name != name) {
							that.devices.push({
								name: name,
								deviceId: deviceId,
								services: [],
								RSSI: RSSI
							});
							store.commit('global/set', {name:"BleDevice",value:JSON.parse(JSON.stringify(that.devices))})
						}
					}
				}
			}
			if (defaultDeviceId !='' && deviceId == defaultDeviceId) { //此处是固定的打印机id，根据需要修改
				//uni.hideLoading();
				store.commit('global/set', {name:"isFoundDefault",value:true})
				this.stopFindBule() //重要代码，找到相应蓝牙后应停止搜索
			}
		});
	},

	stopFindBule() {
		console.log('停止搜寻附近的蓝牙外围设备---------------');
		store.commit('global/set', {name:"IsSearching",value:false})
		uni.stopBluetoothDevicesDiscovery({
			success(res) {
				// console.log(res);
			}
		});
	},
	//初始化
	initBleConn() {
		var that = this;
		store.commit('global/set', {name:"isFoundDefault",value:false})
		let item=global.state.DefaultBlePrinter;
		if(item.deviceId=='' || item.deviceId ==undefined){
			uni.showToast({
				title: '没有默认打印机.',
				icon: 'none'
			})
			return;
		}
		//先断开
		this.destroyed();
		//打开
		this.searchBle(item.deviceId);
		
		const TIME_COUNT = 8;
		if (!this.timer) {
			this.count = TIME_COUNT;
			this.timer = setInterval(() => {
				if (this.count > 0 && this.count <= TIME_COUNT) {
					this.count--;
					if(global.state.isFoundDefault){
						clearInterval(this.timer);
						this.timer = null;
						that.onConn(item);
					}
				} else {
					clearInterval(this.timer);
					this.timer = null;
					that.onConn(item);
					
					//跳转的页面写在此处
					// if(RunType==0){
					// 	that.onDevice(deviceId);
					// 	RunType=1;
					// 	this.count=TIME_COUNT;
					// }
				}
			}, 1000)
		}
	},
	
	onConn(item) {
		var that = this;
		// 先断开之前的蓝牙

		console.log('连接蓝牙---' + item.deviceId);
		
		let deviceId = item.deviceId;
		uni.createBLEConnection({
			deviceId: deviceId,
			timeout: 20000,
			success: function(res) {
				console.log(res);
				uni.hideLoading();
				if (res.errMsg == 'createBLEConnection:ok') {
					console.log('连接蓝牙-[' + item.name + ']--成功');
					store.commit('global/set', {name:"DefaultBlePrinter",value:JSON.parse(JSON.stringify(item))})
					setTimeout(function() {
						that.getBLEServices(deviceId);
					}, 2000);
					//连接成功 关闭搜索
					// setTimeout(function() {
					// 	that.senBleLabel();
					// }, 2100);
					that.stopFindBule()
				} else {
					// uni.showToast({
					// 	title: '连接失败！',
					// 	icon: 'none'
					// })
					that.stopFindBule()
					return;
				}
			},
			fail: function(err) {
				console.log(err)
				//uni.hideLoading();
				// uni.showToast({
				// 	title: '连接错误！',
				// 	icon: 'none'
				// })
				that.stopFindBule()
			}
		});
	},

	getBLEServices(_deviceId) {
		var that = this;
		let deviceId = _deviceId;
		// console.log('获取蓝牙设备所有服务(service)。---------------');
		uni.getBLEDeviceServices({
			// 这里的 deviceId 需要已经通过 createBLEConnection 与对应设备建立链接
			deviceId: deviceId,
			complete(res) {
				// console.log(res);
				let serviceId = '';
				for (var s = 0; s < res.services.length; s++) {
					// console.log(res.services[s].uuid);
					let serviceId = res.services[s].uuid;
					uni.getBLEDeviceCharacteristics({
						// 这里的 deviceId 需要已经通过 createBLEConnection 与对应设备建立链接
						deviceId: deviceId,
						// 这里的 serviceId 需要在 getBLEDeviceServices 接口中获取
						serviceId: serviceId,
						success(res) {
							var re = JSON.parse(JSON.stringify(res));
							// console.log('deviceId = [' + deviceId + ']  serviceId = [' + serviceId + ']');
							for (var c = 0; c < re.characteristics.length; c++) {
								if (re.characteristics[c].properties.write == true) {
									let uuid = re.characteristics[c].uuid;
									// console.log(' deviceId = [' + deviceId + ']  serviceId = [' + serviceId + '] characteristics=[' + uuid);

									for (var index in that.devices) {
										if (that.devices[index].deviceId == deviceId) {
											that.devices[index].services.push({
												serviceId: serviceId,
												characteristicId: uuid
											});
											if(that.devices[index].services.length==1){
												var printInfo={
														isConnected:true,
														deviceId:that.devices[index].deviceId,
														serviceId:that.devices[index].services[0].serviceId,
														characteristicId:that.devices[index].services[0].characteristicId
													}
												store.commit('global/set', {name:"CurrPrinterInfo",value:printInfo})
												store.commit('global/set', {name:"isShowSearch",value:false})
											}
											break;
										}
									}
									// console.log(JSON.stringify(that.devices));
								}
							}
						}
					});
				}
			},
			fail(res) {
				// console.log(res);
			}
		});
	},
	senBlData( uint8Array) {
		let currPrinterInfo=global.state.CurrPrinterInfo;
		if(!currPrinterInfo.isConnected){
			return;
		}
		
		let deviceId = currPrinterInfo.deviceId;
		let serviceId = currPrinterInfo.serviceId;
		let characteristicId = currPrinterInfo.characteristicId;
		// console.log('************deviceId = [' + deviceId + ']  serviceId = [' + serviceId + '] characteristics=[' +
		// 	characteristicId + ']');
		var uint8Buf = Array.from(uint8Array);

		function split_array(datas, size) {
			var result = {};
			var j = 0;
			for (var i = 0; i < datas.length; i += size) {
				result[j] = datas.slice(i, i + size);
				j++;
			}
			// console.log(result);
			return result;
		}
		var sendloop = split_array(uint8Buf, 20);
		// console.log(sendloop.length)
		function realWriteData(sendloop, i) {
			var data = sendloop[i];
			if (typeof data == 'undefined') {
				return;
			}
			// console.log('第【' + i + '】次写数据' + data);
			var buffer = new ArrayBuffer(data.length);
			var dataView = new DataView(buffer);
			for (var j = 0; j < data.length; j++) {
				dataView.setUint8(j, data[j]);
			}
			uni.writeBLECharacteristicValue({
				deviceId,
				serviceId,
				characteristicId,
				value: buffer,
				success(res) {
					realWriteData(sendloop, i + 1);
				}
			});
		}
		var i = 0;
		realWriteData(sendloop, i);
	},
	ab2hex(buffer) {
		const hexArr = Array.prototype.map.call(
			new Uint8Array(buffer),
			function(bit) {
				return ('00' + bit.toString(16)).slice(-2)
			}
		)
		return hexArr.join('')
	},
	senBleData(data = '') {
		if (data == '')
			return;
		let that = this;	

		setTimeout(() => {
			that.senBlData(data);
		})
	}
	
}

module.exports = {
	BlePrinter: BlePrinter
}
