<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view>工菲记录</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="12"><span class="text-no">{{index+1}}.</span> {{item.TNo}}-{{item.TName}}</u-col>
						</u-row>
						<u-row>
							<u-col span="4">
								<span style="margin-left:10rpx;">
									{{item.TColor}}
								</span>
								<span>
									{{item.TSize}}
								</span>
								<span v-if="globalInfo.MyQrcode">
									{{item.TStyle}}
								</span>
							</u-col>
							<u-col span="2" @click="edit(item)">
								<span>
									{{item.TQuantityB}}
								</span>
							</u-col>
							<u-col span="5" @click="edit(item)">
								{{item.TDt.substring(0,10)}}
							</u-col>
							<u-col v-if="canEdit" span="1" @click="edit(item)">
								<u-icon name="edit-pen-fill" color="#2979ff" size="24"></u-icon>
							</u-col>
						</u-row>
					</view>
					<view>
						<view class="custom-btn">
							<u-button type="primary"  @click="initPrinter()">打印</u-button>
						</view>
					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				<view class="page-box" v-if="showPage==1">
					<view>
						<u--form>
							<u-form-item label="编号:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TNo}}
							</u-form-item>
							<u-form-item label="名称:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TName" border="none" placeholder="输入名称" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="数量:" :label-width="labelWidth" borderBottom>
								<u--input type="number" v-model="itemRecord.TQuantity" border="none" placeholder="输入数量" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="合格数量:" :label-width="labelWidth" borderBottom>
								<u--input type="number" v-model="itemRecord.TQuantityB" border="none" placeholder="输入合格数量" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="属性:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TColor" border="none" placeholder="输入颜色/属性" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="规格:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TSize" border="none" placeholder="输入尺码/规格" :customStyle="inputStyle"></u--input>
							</u-form-item>
						</u--form>
					</view>
					<view>
						<view class="custom-btn">
							<u-button type="primary"  @click="save(1)">储存</u-button>
						</view>
						<view class="custom-btn">
							<u-button type="warning" @click="save(4)">删除</u-button>
						</view>
					</view>
					
				</view>
			</scroll-view>
			
			<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelDelete" @confirm="save(2)" ref="uModal"
				:async-close="false" cancel-text="取消" confirm-text="确认删除">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
						<text class="font-red">操作删除后记录无法恢复. </text>
				</view>
			</u-modal>
			
			<u-modal :show="showPrint" title="确认打印" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelPrint" @confirm="print()" ref="uModal"
				:async-close="false" cancel-text="取消" confirm-text="打印">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u--form>
						<u-form-item :labelWidth="labelWidth" label="打印版面" borderBottom
							@click="showPrintSize=true;">
							<u--input v-model="pSizeName" disabled disabledColor="#ffffff" placeholder="请选择打印版面大小" border="none">
							</u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
					</u--form>
				</view>
			</u-modal>

			<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
				:async-close="false" cancel-text="重设" confirm-text="搜索">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
							<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="裁床号" borderBottom
							@click="showSn=true; hideKeyboard()">
							<u--input v-model="sn" disabled disabledColor="#ffffff" placeholder="请选择裁床号" border="none">
							</u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="款号" borderBottom
							@click="showStyle=true; hideKeyboard()">
							<u--input v-model="style" disabled disabledColor="#ffffff" placeholder="请选择款号"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="订单号" borderBottom
							@click="showSaleNo=true; hideKeyboard()">
							<u--input v-model="saleNo" disabled disabledColor="#ffffff" placeholder="请选择订单号"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="制单号" borderBottom
							@click="showMoType=true; hideKeyboard()">
							<u--input v-model="moType" disabled disabledColor="#ffffff" placeholder="请选择制单号"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="开始日"
							@click="showDt1=true;current=1;">
							{{textDt1}}
							<u-datetime-picker :show="showDt1" v-model="dt1" mode="date"
								closeOnClickOverlay @confirm="confirm" @cancel="cancel"
								@change="change" @close="close">
							</u-datetime-picker>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="结束日"
							@click="showDt2=true;current=2;">
							{{textDt2}}
							<u-datetime-picker :show="showDt2" v-model="dt2" mode="date"
								closeOnClickOverlay @confirm="confirm" @cancel="cancel"
								@change="change" @close="close">
							</u-datetime-picker>
						</u-form-item>
					</u-form>
				</view>
			</u-modal>
			<u-picker
				:show="showSn"
				:columns="snLists"
				keyName="_id"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showStyle"
				:columns="styleLists"
				keyName="_id"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showSaleNo"
				:columns="saleNoLists"
				keyName="_id"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showMoType"
				:columns="moTypeLists"
				keyName="_id"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-action-sheet :show="showPrintSize" :actions="sizeLists" title="请选择打印版面大小" :description="sizeLists[this.pSize].name"
				@close="showPrintSize = false" @select="sizeSelect">
			</u-action-sheet>
		</view>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		getBarcode,
		saveBarcode
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				labelWidth: 100,
				current: 0,
				showPage: 0,
				changedDt: false,
				canEdit: false,
				canApply: false,
				itemIdx: 0,
				itemRecord: {},
				keyword: '',
				moType:'',
				moTypeLists:[],
				showMoType:false,
				pSize: 0,
				pSizeName: 0,
				showPrint:false,
				showPrintSize:false,
				sizeLists:[{name:'40mm X 80mm',value:0},{name:'50mm X 70mm ',value:1},{name:'50mm X 80mm ',value:2},{name:'30mm X 40mm',value:3},{name:'30mm X 60mm',value:4}],
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
				showSaleNo: false,
				saleNoLists:[{name:''}],
				sn: '',
				style: '',
				saleNo: '',
				snLists: [{
						name: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputStyle:'backgroundColor:#fff;margin:4rpx;'
				inputStyle:'',
				customBtn:{
					color:'red',
					margin:'30rpx'
				}

			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				adminWrite: 'user/adminWrite',
				adminApply: 'user/adminApply',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			this.urlLists = this.globalInfo.UrlLists
			var pages = getCurrentPages() //获取加载的页面
			var currentPage = pages[pages.length - 1] //获取当前页面的对象
			var url = "/" + currentPage.route //当前页面url
			for (var i = 0; i < this.urlLists.length; i++) {
				if (this.urlLists[i].url == url) {
					this.tabValue = i;
					break;
				}
			}
			this.canEdit=this.adminWrite[6];
			this.canApply=this.adminApply[6];
			this.pSizeName=this.sizeLists[this.pSize].name;
			this.changedDt=true;
			this.loadmore();
			console.log('minDate,maxDate',this.minDate,this.maxDate);
			console.log('dt1,dt2',this.dt1,this.dt2);
			console.log('textDt1,textDt2',this.textDt1,this.textDt2);
		},
		onReady() {
			// 微信小程序需要用此写法
			//#ifdef MP-WEIXIN
			//this.$refs.dtP1.setFormatter(this.formatter)
			//this.$refs.dtP2.setFormatter(this.formatter)
			//#endif
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			

			reachBottom() {
				if (this.loadStatus == 'loading' || this.loadStatus == 'nomore') {
					return
				}
				this.loadStatus = "loading"
				setTimeout(() => {
					this.loadmore();
				}, 1200);

			},
			doSearch() {
				this.page = 1
				this.showFilter = false
				this.dataLists = []
				this.loadmore()
			},
			edit(item,itemIdx){
				this.itemIdx=itemIdx
				this.itemRecord=item
				this.showPage=1
			},
			sizeSelect(e){
				this.pSize=e.value;
				this.pSizeName=this.sizeLists[this.pSize].name;
			},
			async initPrinter(){
				if(this.globalInfo.DefaultBlePrinter.name=='' || this.globalInfo.DefaultBlePrinter.name == undefined){
					let url='/pagesPrinter/printer';
					uni.navigateTo({
						url
					})
				}else{
					this.showPrint=true;
					if(!this.globalInfo.CurrPrinterInfo.isConnected) {
						await this.BlePrinter.initBleConn();
					}
				} 
			},
			print(){
				let data=JSON.stringify(this.dataLists);
				//let url='/pagesPrinter/barcode?size='+this.pSize+'&data='+ encodeURIComponent(data);
				let url='/pagesPrinter/barcode?size='+this.pSize+'&data='+ data;
				uni.navigateTo({
					url
				})
				this.showPrint=false;
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let actDt=0;
				if(this.changedDt)
					actDt=1;
				this.changedDt=false;
				let params = {
					page: this.page,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					showDel:0,
					actDt,
					keyword:this.keyword,
					sn: this.sn,
					style: this.style,
					saleNo: this.saleNo,
					moType: this.moType
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getBarcode({
					params: params
				});
				let data = res.dataLists
				this.queryInfo = res.queryInfo
				for (let i = 0; i < data.length; i++) {
					this.dataLists.push(data[i])
				}
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				if(this.queryInfo.page==1){
					this.snLists=[]
					this.snLists.push(this.queryInfo.snLists)
					this.styleLists=[]
					this.styleLists.push(this.queryInfo.styleLists)
					this.moTypeLists=[]
					this.moTypeLists.push(this.queryInfo.moTypeLists)
					this.saleNoLists=[]
					this.saleNoLists.push(this.queryInfo.saleLists)
					// if(actDt==1){
					// 	this.saleNoLists=[]
					// 	this.saleNoLists.push(this.queryInfo.saleLists)
					// }
					// if(actDt==1){
					// 	this.saleNoLists=[]
					// 	this.saleNoLists.push(this.queryInfo.saleLists)
					// }
					
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.saleNo=''
				this.showFilter=false
			},
			cancelPrint(){
				this.showPrint=false;
			},
			async save(actType){
				if(actType==4){
					this.showDel=true
					return
				}else{
					this.showDel=false
				}
				let params = {
					actType:actType,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord
				}
				uni.showLoading({
					'title':'正在处理操作...'
				});
				let res = await saveBarcode(params);
				uni.hideLoading();
				if(actType==2)
					this.dataLists.splice(this.itemIdx,1)
				//this.showDel=false
				this.showPage=0
			},
			async configDelete(){
				this.save(2)
			},
			cancelDelete(){
				this.showDel=false
			},
			navigateTo(url) {
				//uni.redirectTo({
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				uni.switchTab({
					url
				})
			},
			changeTabbar(e) {
				this.tabValue = e
				var url = this.urlLists[e].url
				uni.redirectTo({
					url
				})
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
			confirm(e) {
				this.close()
				const timeFormat = uni.$u.timeFormat
				if (this.current == 1) {
					//this.dt1 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt1 = timeFormat(e.value, 'yyyy/mm/dd')
					this.changedDt=true;
				}
				if (this.current == 2) {
					//this.dt2 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt2 = timeFormat(e.value, 'yyyy/mm/dd')
					this.changedDt=true;
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
			snSelect(e) {
				this.sn = e.name
				//this.$refs.form1.validateField('userInfo.sex')
			},
			styleSelect(e) {
				this.style = e.name
			},
			saleNoSelect(e) {
				this.saleNo = e.name
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},			
			selectPicker(e) {
				let item = e.value[0];
				if(this.showSn)
					this.sn=item._id
				if(this.showStyle)
					this.style=item._id
				if(this.showSaleNo)
					this.saleNo=item._id
				if(this.showMoType)
					this.moType=item._id
				this.cancelPicker();
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.cancelPicker()
			},
			cancelPicker() {
				this.showSn = false
				this.showStyle = false
				this.showSaleNo = false
				this.showMoType = false
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

<style lang="scss">
	.item-row {
		padding: 6rpx;
	}
</style>
