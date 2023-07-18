<template>
	<view class="container">			
		<view class="header">
			<u-tabs :list="tabLists" @change="changeTabsVal"></u-tabs>
		</view>
		<view class="body">
			<view>
				日期{{queryInfo.dt1.substr(5,5)}}至{{queryInfo.dt2.substr(5,5)}} 共
				<span class="font-red">
					{{FormatNum(queryInfo.count)}}
				</span> 
				<span>
					筆
				</span>
				<span class="font-red">
					{{FormatNum(queryInfo.num1)}}
				</span> 
				<span>
					  {{unit1||'件'}}
				</span>
				<span v-if="showPrice">
					￥:
				</span>
				<span class="font-red" v-if="showPrice">
					{{FormatNum(queryInfo.num2,2)}}
				</span>
				
			</view>
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box">
					<view class="item-row" :class="'bg-3'" v-for="(item, index) in orderLists" :key="index">
						<u-row>
							<u-col span="12">
								<view class="row-l-r">
									<view><span class="text-no">{{index+1}}. </span>[{{item.TNo||''}}-{{item.TPrice||''}}]</view>
									<view>
										{{item.TDt||''}}
									</view>
								</view>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="12">
							<view class="item-row" :class="index2%2==0?'bg-1':'bg-2'" v-for="(item2, index2) in item.TLists" :key="index2">
								<u-row>
									<u-col span="6">{{item2.TName}} </u-col>
									<u-col span="2">{{item2.TPrice}}</u-col>
									<u-col span="2"> {{item2.TQuantity}} </u-col>
									<u-col span="2"> {{item2.TAmt}} </u-col>
								</u-row>
							</view>
							</u-col>
						</u-row>
					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
			</scroll-view>
		</view>
		
		<u-modal :show="showDel" title="確認刪除" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelDelete" @confirm="configDelete()" ref="uModal"
			:async-close="false" cancel-text="取消" confirm-text="確認刪除">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<text class="font-red">操作刪除後記錄無法恢復. </text>
			</view>
		</u-modal>
		
		<u-modal :show="showFilter" title="搜索條件" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
			:async-close="false" cancel-text="重設" confirm-text="搜索">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="關鍵詞">
						<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="裁床號" borderBottom
						@click="showSn=true; hideKeyboard()">
						<u--input v-model="sn" disabled disabledColor="#ffffff" placeholder="請選擇裁床號" border="none">
						</u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="款號" borderBottom
						@click="showStyle=true; hideKeyboard()">
						<u--input v-model="style" disabled disabledColor="#ffffff" placeholder="請選擇款號"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="工序名稱" borderBottom
						@click="showGx=true; hideKeyboard()">
						<u--input v-model="gxName" disabled disabledColor="#ffffff" placeholder="請選擇工序名稱"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="訂單號" borderBottom
						@click="showSaleNo=true; hideKeyboard()">
						<u--input v-model="saleNo" disabled disabledColor="#ffffff" placeholder="請選擇訂單號"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="制單號" borderBottom
						@click="showMoType=true; hideKeyboard()">
						<u--input v-model="moType" disabled disabledColor="#ffffff" placeholder="請選擇制單號"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="開始日"
						@click="showDt1=true;current=1;">
						{{textDt1}}
						<u-datetime-picker :show="showDt1" v-model="dt1" mode="date"
							closeOnClickOverlay @confirm="confirm" @cancel="cancel"
							@change="change" @close="close">
						</u-datetime-picker>
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="結束日"
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
			keyName="name"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showSaleNo"
			:columns="saleNoLists"
			keyName="TGuid"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showGx"
			:columns="gongxuLists"
			keyName="name"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showStyle"
			:columns="styleLists"
			keyName="name"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showMoType"
			:columns="moTypeLists"
			keyName="name"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		saveOrder,
		getOrder
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				dtIdx:0,
				labelWidth: 80,
				current: 0,
				actType: 0,
				itemIdx: 0,
				showScroll: 0,
				itemRecord:{},
				keyword: '',
				//minDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -730))),
				//maxDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), 365))),
				dt1: Number(new Date()),//Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				changedDt: false,
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showSn: false,
				showStyle: false,
				showSaleNo: false,
				showGx: false,
				saleNoLists:[{name:''}],
				moType:'',
				gxName:'',
				moTypeLists:[],
				showMoType:false,
				saleNo: '',
				sn: '',
				style: '',
				orderLists:[
					{TNo:'A001',
					TPrice:100,
					TDt:'2022/11/25',
					TStatus:0,
					TLists:[
						{TName:'品项-A1',TPrice:10,TQuantity:10,TAmt:100},
						{TName:'品项-A2',TPrice:20,TQuantity:10,TAmt:200},
						{TName:'品项-A3',TPrice:30,TQuantity:10,TAmt:300},
						]
					},
					{TNo:'A002',
					TPrice:100,
					TDt:'2022/11/25',
					TStatus:0,
					TLists:[
						{TName:'品项-A1',TPrice:10,TQuantity:10,TAmt:100},
						{TName:'品项-A2',TPrice:20,TQuantity:10,TAmt:200},
						{TName:'品项-A3',TPrice:30,TQuantity:10,TAmt:300},
						]
					},
					{TNo:'A003',
					TPrice:100,
					TDt:'2022/11/25',
					TStatus:0,
					TLists:[
						{TName:'品项-A1',TPrice:10,TQuantity:10,TAmt:100},
						{TName:'品项-A2',TPrice:20,TQuantity:10,TAmt:200},
						{TName:'品项-A3',TPrice:30,TQuantity:10,TAmt:300},
						]
					},
					{TNo:'A004',
					TPrice:100,
					TDt:'2022/11/25',
					TStatus:0,
					TLists:[
						{TName:'品项-A1',TPrice:10,TQuantity:10,TAmt:100},
						{TName:'品项-A2',TPrice:20,TQuantity:10,TAmt:200},
						{TName:'品项-A3',TPrice:30,TQuantity:10,TAmt:300},
						]
					},
					{TNo:'A005',
					TPrice:100,
					TDt:'2022/11/25',
					TStatus:0,
					TLists:[
						{TName:'品项-A1',TPrice:10,TQuantity:10,TAmt:100},
						{TName:'品项-A2',TPrice:20,TQuantity:10,TAmt:200},
						{TName:'品项-A3',TPrice:30,TQuantity:10,TAmt:300},
						]
					},
				],
				snLists: [{
						name: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],
				gongxuLists: [{
						name: '01',
					}
				],

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				tabLists: [{
						name: '待確認'
					},
					{
						name: '已出庫'
					},
					{
						name: '已完成'
					},
					{
						name: '搜索'
					}
				],
				showPrice:false,
				canReturn:false,
				unit:'',
				unit1:'',
				unit2:'',
				queryInfo: {dt1:timeFormat(new Date(), 'yyyy/mm/dd'),dt2:timeFormat(new Date(), 'yyyy/mm/dd'),num1:0,num2:0,num3:0,isLast:false,page:1}

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
			if(uni.$u.mpShare){
				uni.$u.mpShare.title = this.globalInfo.MpShareTitle;
				let sharePath=this.globalInfo.MpSharePath+'?Domain='+this.globalInfo.Domain;
				if(this.hasLogin){
					uni.$u.mpShare.title =this.userInfo.company_name+ this.globalInfo.MpShareTitle;
					sharePath=sharePath+'&TUserId1='+this.userInfo.company_no;
				}
				console.log(sharePath)
				uni.$u.mpShare.path = sharePath;
			}
			this.urlLists = this.globalInfo.UrlLists
			var pages = getCurrentPages() //獲取加載的頁面
			var currentPage = pages[pages.length - 1] //獲取當前頁面的對象
			var url = "/" + currentPage.route //當前頁面url
			for (var i = 0; i < this.urlLists.length; i++) {
				if (this.urlLists[i].url == url) {
					this.tabValue = i;
					break;
				}
			}
			this.textDt1= this.now()
			this.textDt2= this.now()
			this.changedDt=true;
			this.loadmore();
		},
		onReady() {
			// 微信小程序需要用此寫法
			//this.$refs.dtP1.setFormatter(this.formatter)
			//this.$refs.dtP2.setFormatter(this.formatter)
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
				this.queryInfo.isLast=false
				this.showFilter = false
				this.dataLists = []
				this.loadmore()
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let actDt=0;
				if(this.changedDt)
					actDt=1;
				this.changedDt=false;
				if(!this.hasLogin){
					uni.showToast({
						title:'請先登錄.'
					})
					return;
				}
				if(this.queryInfo.isLast)
					return
				let params = {
					actType:12,
					showDel:0,
					actDt,
					dtIdx:this.dtIdx,
					page: this.page,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					keyword:this.keyword,
					sn: this.sn,
					style: this.style,
					gongxu: this.gxName,
					moType: this.moType||'',
					saleNo: this.saleNo
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加載數據...'
				});
				//get has {}
				let res = await getOrder({
					params
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
					this.gongxuLists=[]
					this.gongxuLists.push(this.queryInfo.gongxuLists)
					this.moTypeLists=[]
					this.moTypeLists.push(this.queryInfo.moTypeLists)
					if(actDt==1){
						this.saleNoLists=[]
						this.saleNoLists.push(this.queryInfo.saleLists)
					}
					let configCorp='01000000000000000000000';
					if(this.queryInfo.company!=null){
						this.unit=this.queryInfo.company.TUnit
						this.unit1=this.queryInfo.company.TUnit1
						this.unit2=this.queryInfo.company.TUnit2
						configCorp=this.queryInfo.company.TConfigCorp
					}
					//substr從0開始
					if(configCorp.length>3){
						if(configCorp.substr(0,1)=="1")
							this.canReturn=true
						if(configCorp.substr(1,1)=="0")
							this.showPrice=true
					}
					
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			edit(actType,item,itemIdx){
				this.itemRecord=item
				this.itemIdx=itemIdx
				if(actType==4){
					this.showDel=true
					return
				}
				//this.actType=actType
			},
			async configDelete(){
				this.itemRecord.TIsDelete=1;
				let params = {
					actType:2,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord,
					delLists:[],
					dataLists:[]
				}
				uni.showLoading({
					'title':'正在處理操作...'
				});
				//post wu {}
				let res = await saveOrder(params);
				uni.hideLoading();
				this.dataLists.splice(this.itemIdx,1)
				this.showDel=false
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
			changeTabbar(e) {
				this.tabValue = e
				let url = this.urlLists[e].url
				uni.redirectTo({
					url
				})
			},
			changeTabsVal(item) {
				let idx=item.index
				this.dtIdx=idx
				if(idx==3){
					this.showFilter=true
					return
				}
				if(idx==0){
					this.textDt1= this.now()
					this.textDt2= this.now()
				}
				if(idx==1){
					this.textDt1= this.monthDate(1, 0)
					this.textDt2= this.monthDate(0, 0)
				}
				if(idx==2){
					this.textDt1= this.monthDate(1, -1)
					this.textDt2= this.monthDate(0, -1)
				}
				this.changedDt=true;
				this.showScroll = item.index
				this.keyword=''
				this.sn=''
				this.style=''
				this.moType=''
				this.saleNo=''
				this.gxName=''
				this.doSearch()
			},
			cancelFilter(){
				this.keyword=''
				this.sn=''
				this.gxName=''
				this.style=''
				this.saleNo=''
				this.moType=''
				this.showFilter=false
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
			gxSelect(e) {
				this.gxName = e.name
			},
			saleNoSelect(e) {
				//console.log(e)
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
					this.sn=item.name
				if(this.showStyle)
					this.style=item.name
				if(this.showMoType)
					this.moType=item.name
				if(this.showSaleNo)
					this.saleNo=item.TGuid
				if(this.showGx)
					this.gxName=item.name
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
				this.showGx = false
			},
			changeHandlerPicker(e) {
				this.changePicker(e)
				const {
					columnIndex,
					value,
					values,
					index,
					// 微信小程序無法將picker實例傳出來，只能通過ref操作
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
	
</style>
