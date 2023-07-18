<template>
	<view class="container">
		<view class="header">
			<view class="row-l-r">
				<view class="title-text">工单扫码检查</view>
			</view>
			
		</view>
		
		<view class="scan-body">
			<view style="padding-top: 5px;">
				<u-checkbox-group v-model="showChecked" placement="row">
					<u-checkbox name="showFinish" activeColor="green" label="显示完成"></u-checkbox>
					<u-checkbox name="showNotFinish" activeColor="green" label="显示未完成"></u-checkbox>
				</u-checkbox-group>
								
			</view>
			<view>
				<u-row>
					<u-col :span="10">
						<span style="color:dodgerblue;">
							{{barcode.TNo || ''}} 
						</span>
						<span style="color:darkcyan;">
							{{barcode.TName || '无工单'}}
						</span>
					</u-col>
					<u-col :span="2">
						<span style="color:crimson;font-size: 1rem;">
							{{barcode.TQuantityB  || ''}}
						</span>
					</u-col>
				</u-row>
			</view>
			<view v-if="barcode.TName!=''">
				<view class="text-green" v-if="isFinish">
					已经全部完成!
				</view>
				<view class="text-red" v-if="!isFinish">
					未完成!
				</view>
			</view>

			<view>
				<scroll-view scroll-y="true" class="sv" :style="{height:navHeight+'px'}">
					<view style="height: 10px;"></view>
					<view>
						<u-row  v-for="(item, index) in skuLists" v-if="showNf(item)">
							<span style="color:red;">{{item.TInfo}}</span>
						</u-row>
					</view>
					<view>
						<u-row  v-for="(item, index) in skuLists" v-if="showF(item)">
							<span>{{item.TInfo}}</span>
						</u-row>
					</view>
				</scroll-view>
			</view>
			
		</view>
		
		<view class="footer">
			<u-row>
				<u-col :span="12" stop>
					<view class="btn-p">
						<u-button @click="doScan" type="primary"> 扫 码 </u-button>
					</view>
				</u-col>
			</u-row>
		</view>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		getAppSku,
		getParameter,
		saveOrder,
		saveAppOrder,
		getBarcode,
		getBarcodeSku,
		postQRCode
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				page:1,
				resMsg: {
					errCode: -1,
					msg: '',
					showError: false,
					showSuccess: false,
					eMsg: '',
					sMsg: '',
					msgRemark: ''
				},
				snType:0,
				isFinish:false,
				showChecked:["showFinish", "showNotFinish"],
				isPosting:false,
				startTSn:'',
				endTSn:'',
				//selectedBarcode:{},
				labelWidth:90,
				postBtnSpan:12,
				loadStatus: ['loadmore', 'loadmore', 'loadmore', 'loadmore'],
				barcode:{TGuid:'',TNo:'',TName:'',TQuantityB:'',TQuantity:''},
				skuLists: [],
				quantity: '',
				checkboxSkus: [],
				uid: '',
				showBarcode: false,
				tabValue: 1,
				nickname: '',
				showProductInfo: false,
				urlLists: [],
				barcodeLists: [],
				scanTypeLists: [],
				//minDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -730))),
				//maxDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), 365))),
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showSn: false,
				showStyle: false,
				skuNo:'',
				sn: '',
				style: '',
				snLists: [{
						name: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],
				pH:0, //窗口高度
				navHeight:0, //元素的所需高度
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			this.urlLists = this.globalInfo.UrlLists
			this.scanTypeLists=this.globalInfo.ScanTypeLists
		},
		onShow() {
			//let _this=this;
			//this.$nextTick(()=>{_this.checkboxSkus = _this.globalInfo.DefaultSkus;});
			this.checkboxSkus = this.globalInfo.DefaultSkus;
			if(this.globalInfo.ScanType==1 || this.globalInfo.ScanType==2 || this.globalInfo.ScanType==4 || this.globalInfo.ScanType==5)
				this.postBtnSpan=12
			else
				this.postBtnSpan=6
		},
		onReady() {
			let that=this;
			uni.getSystemInfo({ //调用uni-app接口获取屏幕高度
				success(res) { //成功回调函数
					that._data.pH=res.windowHeight //windoHeight为窗口高度，主要使用的是这个
					let titleH=uni.createSelectorQuery().select(".sv"); //想要获取高度的元素名（class/id）
					titleH.boundingClientRect(data=>{
						let pH=that._data.pH; 
						that._data.navHeight=pH-data.top-50  //计算高度：元素高度=窗口高度-元素距离顶部的距离（data.top）
					}).exec()
				}
			})
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set'
			}),
			showF(item){
				if(this.showChecked.includes('showFinish') && item.isFinish)
					return true;
				else
					return false;
			},
			showNf(item){
				if(this.showChecked.includes('showNotFinish') && !item.isFinish)
					return true;
				else
					return false;
			},
			setCheckBoxShow(item){
				let canShow=true;
				if(item.isFinish && this.globalInfo.HideFinish)
					canShow=false;
				return canShow;
			},
			checkboxChange(val) {
				this.checkboxSkus = val;
			},
			clearMsg(){
				this.resMsg.msg=''
				this.resMsg.eMsg=''
				this.resMsg.sMsg=''
			},
			doScan() {
				
				this.clearMsg()
				this.barcode={TGuid:'',TNo:'',TName:'',TQuantityB:'',TQuantity:''};
				this.skuLists=[];
				this.skuNo='';
				let that = this
				uni.scanCode({
					success: function(res) {
						that.getSkuList(res.result)
					}
				});
			},
			
			async getSkuList(barcodeText) {
				this.skuLists=[];
				//默认二维码, 工厂条码快递码
				let barcodeType=0;
				if(this.globalInfo.MyQrcode)
					barcodeType=1;
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					no: barcodeText,
					barcodeType:barcodeType,
					scanType:10
				}
				let res = await getAppSku({params});
				this.barcode= res;
				
				this.skuNo=res.skuNo;
				this.skuLists = this.barcode.TSkuLists;
				this.isFinish=true;
				for(let i=0;i<this.skuLists.length;i++){
					if(!this.skuLists[i].isFinish){
						this.isFinish=false;
						break;
					}
				}
				
			},
			checkPost(){
				let isOk=true;
				if(!this.hasLogin){
					uni.showToast({
						title:'请先登录.'
					})
					isOk=false;
				}
				if(!this.globalInfo.DefaultUser.uid){
					this.resMsg.eMsg='请先设定员工.'
					uni.showToast({
						title:'请先设定员工.'
					})
					this.navigateTo('/pagesA/ticket/setting');
					isOk=false;
				}
				return isOk;
			},
			//提交 post()
			async post() {
				//this.PlayAudio(3);
				let that = this;
				
				// this.resMsg.msg=res.msg
				// this.resMsg.errCode=res.errCode
				// this.resMsg.eMsg=res.eMsg
				// this.resMsg.sMsg=res.sMsg
				// if(res.record)
				// 	this.skuLists=res.record.TSkuLists
				
				// setTimeout(function() {
				// 	that.isPosting=false;
				// 	that.$nextTick(()=>{that.checkboxSkus = that.globalInfo.DefaultSkus;});
				// }, 1200);
			},
			
			barcodeSelected(e){
				this.barcode= e;
				this.skuLists = this.barcode.TSkuLists;
			},
			close() {
				if (this.current == 1)
					this.showDt1 = false
				if (this.current == 2)
					this.showDt2 = false
			},
			cancel(idx) {
				this.close()
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
			},
			confirm(e) {
				this.close()
				const timeFormat = uni.$u.timeFormat
				if (this.current == 1) {
					this.textDt1 = timeFormat(e.value, 'yyyy/mm/dd')
				}
				if (this.current == 2) {
					this.textDt2 = timeFormat(e.value, 'yyyy/mm/dd')
				}
			
			},
			change(e) {
				//
			},
			formatter(type, value) {
				if (type === 'year') {
					return `${value}年`
				}
				if (type === 'month') {
					return `${value}月`
				}
				if (type === 'day') {
					return `${value}日`
				}
				return value
			},
			styleSelect(e) {
				this.style = e.name
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},			
			selectPicker(e) {
				let item = e.value[0];
				if(this.showSn){
					if(this.snType==0)
						this.sn=item.name
					if(this.snType==1)
						this.startTSn=item.name
					if(this.snType==2)
						this.endTSn=item.name
				}
				if(this.showStyle)
					this.style=item.name
				if(this.showBarcode){
					this.barcode= item;
					this.skuLists = this.barcode.TSkuLists;
				}
				this.cancelPicker();
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.cancelPicker()
			},
			cancelPicker() {
				this.showSn = false
				this.showStyle = false
				this.showBarcode = false
			},
			changeHandlerPicker(e) {
				this.changePicker(e)
				const {
					columnIndex,
					value,
					values,
					index,
					// 微信小程序无法将picker实例传出来，只能通过ref操作
					picker = this.$refs.uPicker3
				} = e
				if (columnIndex === 0) {
					//多列
					//picker.setColumnValues(1, this.columnData[index])
				}
			}

		}
	}
</script>



<style lang="scss" scoped>
 
	.text-green{
		color:green;
	}
	.text-red{
		color:red;
	}
	
</style>
