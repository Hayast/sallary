<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<span>打印工菲</span>
				<u-icon name="setting-fill" color="#2979ff" size="24" @click="setPrinter()"></u-icon>
			</view>
		</view>
		<view class="body page-box">
			<view class="s-v text-center">
				打印机: {{globalInfo.DefaultBlePrinter.name || '未设置打印机'}}
			</view>

			<view class="s-v">
				<view v-if="globalInfo.CurrPrinterInfo.isConnected" style="margin-top: 20px;">
					<u-button type="primary" @click="doPrint()">打印</u-button>
				</view>
				<view v-if="false" style="margin-top: 20px;">
					<u-button type="primary" @click="labelTest()">测试</u-button>
				</view>
				<view v-if="!globalInfo.CurrPrinterInfo.isConnected" style="margin-top: 20px;">
					<u-button type="primary" @click="setPrinter()">设置打印机</u-button>
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
				dataLists: [],
				pSize: 0,
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad: function(options) {
			if (this.globalInfo.PrinterType == 0) {
				this.isLabel = false;
			}
			this.pSize = options.size;
			this.dataLists = JSON.parse(options.data);
			this.initPrinter();
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			setPrinter() {
				let url = '/pagesPrinter/printer';
				uni.navigateTo({
					url
				})
			},
			async initPrinter() {
				if (this.globalInfo.DefaultBlePrinter.name != '' && this.globalInfo.DefaultBlePrinter.name !=
					undefined && !this.globalInfo.CurrPrinterInfo.isConnected) {
					await this.BlePrinter.initBleConn();
				}
				if (this.globalInfo.CurrPrinterInfo.isConnected) {
					this.doPrint();
				}
			},
			doNothing() {
				return;
			},
			async initBleConn() {
				await this.BlePrinter.initBleConn();
			},
			doPrint() {
				var _pageSize = this.size || 0
				var _PrintType = this.globalInfo.PrinterType
				if (_PrintType == '1') {
					this.senBleLabel(_pageSize); //标签打印
				} else
					this.senBleLabel2(); //票据打印

			},
			async senBleLabel(_pageSize) {
				//标签模式
				let that = this;
				var command = this.tscPrinter.jpPrinter.createNew(); 
				var printData
				//设置大小
				if (_pageSize == 0) {
					command.setSize(40, 80);
					command.setGap(2);
					this.dataLists.forEach(item => {              
						command.setCls();
						command.setText(20, 40, 'TSS24.BF2', 1, 1, '裁床号:' + item.TSn);
						command.setText(20, 80, 'TSS24.BF2', 1, 1, '编号:' + item.TNo);
						command.setQR(50, 120, 'L', 8, 'A', item.TNo);
						command.setText(20, 320, 'TSS24.BF2', 1, 1, '名称:' + item.TName);
						command.setText(20, 360, 'TSS24.BF2', 1, 1, '颜色:' + item.TColor + '  ' + item.TSize +'码');
						command.setText(20, 400, 'TSS24.BF2', 1, 1, '数量:' +item.TQuantity+ '  扎号:' + item.TNum1);
						if(item.TRemark!='' && item.TRemark!='null' && item.TRemark!=null && item.TRemark!=undefined)
							command.setText(20, 440, 'TSS24.BF2', 1, 1, item.TRemark);
						//command.setText(20, 460, 'TSS24.BF2', 1, 1, this.userInfo.company_name);
						command.setPagePrint()
					});
				}
				if (_pageSize == 1 || _pageSize == 2) {
					if (_pageSize == 1)
						command.setSize(50, 70);
					if (_pageSize == 2)
						command.setSize(50, 80);
					command.setGap(2);
					this.dataLists.forEach(item => {
						item=this.dataList[i];
						command.setCls();
						command.setText(20, 40, 'TSS24.BF2', 1, 1, '裁床号:' + item.TSn);
						command.setText(20, 80, 'TSS24.BF2', 1, 1, '编号:' + item.TNo);
						command.setQR(120, 120, 'L', 8, 'A', item.TNo);
						command.setText(20, 320, 'TSS24.BF2', 1, 1, '名称:' + item.TName)
						command.setText(20, 360, 'TSS24.BF2', 1, 1, '数量:' + '颜色:' + item.TColor + '  ' + item.TSize +'码');
						command.setText(20, 400, 'TSS24.BF2', 1, 1, item.TQuantity+'  扎号:' + item.TNum1);
						if(item.TRemark!='' && item.TRemark!='null' && item.TRemark!=null && item.TRemark!=undefined)
							command.setText(20, 440, 'TSS24.BF2', 1, 1, item.TRemark);
						command.setPagePrint();
					});
				}
				if (_pageSize == 3 || _pageSize == 4) {
					if (_pageSize == 3)
						command.setSize(30, 40);
					if (_pageSize == 4)
						command.setSize(30, 60);
					command.setGap(2);
					this.dataLists.forEach(item => {
						item=this.dataList[i];
						command.setCls();
						command.setQR(10, 20, 'L', 5, 'A', item.TNo);
						command.setText(120, 20, 'TSS24.BF2', 1, 1, item.TNo);
						command.setText(120, 50, 'TSS24.BF2', 1, 1, item.TColor + '  ' + item.TSize + '码');
						command.setText(120, 80, 'TSS24.BF2', 1, 1, '数量:' + item.TQuantity);
						command.setText(120, 110, 'TSS24.BF2', 1, 1, '裁床:' + item.TSn + '/' + item.TNum1);
						command.setPagePrint();
					});
				}
				printData=command.getData()
				await this.BlePrinter.senBleData(printData);
				uni.navigateBack();
			},
			async senBleLabel2() {
				//票据模式
				var command = this.escPrinter.jpPrinter.createNew();
				//command.init();
				var item
				var printData;
				for (var i = 0; i < this.dataList.length; i++) {
					item=this.dataList[i];
					command.setText('编号: ' + item.TNo);
					command.setPrintAndFeedRow(1)
					command.setText('名称: ' + item.TName);
					command.setPrintAndFeedRow(1)
					command.setText('颜色: ' + item.TColor + '尺码:' + item.TSize);
					command.setPrintAndFeedRow(1)
					command.setText('数量: ' + item.TQuantity);
					command.setPrintAndFeedRow(1)
				}
				printData=command.getData()
				await this.BlePrinter.senBleData(printData);
				uni.navigateBack();
			},

			async labelTest() {
				var _PrintType = this.globalInfo.PrinterType;
				var printData
				if (_PrintType == '1') {
					//标签打印
					let command = this.tscPrinter.jpPrinter.createNew();
					command.setSize(50, 70);
					command.setGap(2);
					command.setCls();
					command.setQR(120, 50, 'L', 8, 'A', '13580704353');
					command.setText(20, 300, 'TSS24.BF2', 1, 1, '联络手机标签打印');
					command.setPagePrint();
					printData=command.getData();
				} else{
					//票据打印
					var command = this.escPrinter.jpPrinter.createNew();
					command.setText('联络手机: 13580704353');
					command.setPrintAndFeedRow(1)
					command.setText('名称: 票据打印打印');
					command.setPrintAndFeedRow(1);
					command.setText('颜色: 白色 尺码: XL');
					command.setPrintAndFeedRow(1)
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
	.s-v {
		padding: 15px;
	}

	.s-v-u-s {
		padding: 15px;
		display: flex;
		flex-flow: row nowrap;
		//justify-content: space-between;
	}
</style>
