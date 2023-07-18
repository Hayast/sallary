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
					笔
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
					<view class="item-row" :class="index%2==0?'bg-1':'bg-2'" v-for="(item, index) in dataLists" :key="index">
						<u-row>
							<u-col span="12">
								<view>
									<span class="text-no">{{index+1}}. </span>[{{item.TNo||''}}-{{item.TNum1||''}}]{{item.TSaleNo||''}} <span style="color:royalblue;">{{item.TType||''}}</span> {{item.TName||''}}{{item.TSkuName||''}}
								</view>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="8">
								<view>
									<span v-if="showPrice" class="price-1">¥:</span>
									<span v-if="item.TQuantity>0">
										{{FormatNum(item.TQuantity)}}
									</span>
									<span v-if="item.TQuantity<0" style="color:red;">
										{{FormatNum(item.TQuantity)}}
									</span>
									<span v-if="showPrice">
										*{{FormatNum(item.TPrice) || ''}}={{FormatNum(item.TAmt) || ''}}
									</span>
								</view>
							</u-col>
							<u-col span="3">
								<view>
									<span class="price-date">{{item.TDt.substr(5,11)}}</span>
								</view>
							</u-col>
							<u-col v-if="canReturn && item.TStatus<1 && item.TQuantity>0" span="1" @click="edit(4,item,index)">
								<u-icon name="trash-fill" color="#ffaa00" size="24"></u-icon>
							</u-col>
						</u-row>
					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
			</scroll-view>
		</view>
		
		<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelDelete" @confirm="configDelete()" ref="uModal"
			:async-close="false" cancel-text="取消" confirm-text="确认删除">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<text class="font-red">操作删除后记录无法恢复. </text>
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
					<u-form-item :labelWidth="labelWidth" label="工序名称" borderBottom
						@click="showGx=true; hideKeyboard()">
						<u--input v-model="gxName" disabled disabledColor="#ffffff" placeholder="请选择工序名称"
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
			:show="showSaleNo"
			:columns="saleNoLists"
			keyName="_id"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showGx"
			:columns="gongxuLists"
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
				isLoading:false,
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
				saleNoLists:[{_id:''}],
				moType:'',
				gxName:'',
				moTypeLists:[],
				showMoType:false,
				saleNo: '',
				sn: '',
				style: '',
				snLists: [{
						_id: '000001',
					}
				],
				styleLists: [{
						_id: '01',
					}
				],
				gongxuLists: [{
						_id: '01',
					}
				],

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				tabLists: [{
						name: '当天'
					},
					{
						name: '当月'
					},
					{
						name: '上月'
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
			var pages = getCurrentPages() //获取加载的页面
			var currentPage = pages[pages.length - 1] //获取当前页面的对象
			var url = "/" + currentPage.route //当前页面url
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
			// 微信小程序需要用此写法
			//this.$refs.dtP1.setFormatter(this.formatter)
			//this.$refs.dtP2.setFormatter(this.formatter)
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			reachBottom() {
				if (this.isLoading || this.loadStatus == 'nomore') {
					return
				}
				setTimeout(() => {
					this.loadmore();
				}, 1200);

			},
			doSearch() {
				this.page = 1
				this.queryInfo.isLast=false
				this.showFilter = false
				this.dataLists = []
				setTimeout(() => {
					this.loadmore();
				}, 1200);
			},
			scrolltolower() {
				setTimeout(() => {
					this.loadmore();
				}, 1200);
			},
			async loadmore() {
				let actDt=0;
				if(this.changedDt)
					actDt=1;
				this.changedDt=false;
				if(!this.hasLogin){
					uni.showToast({
						title:'请先登录.'
					})
					return;
				}
				
				if(this.queryInfo.isLast)
					return
				if(this.isLoading){
					console.log(' is loading data.');
					return;
				}
				this.isLoading=true;
				let params = {
					companyNo: this.userInfo.company_no,
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
				try{
					uni.showLoading({
						'title':'正在加载数据...'
					});
				}catch(e){
					//
				}
				
				//get has {}
				try{
					let res = await getOrder({
						params
					});
					let data = res.dataLists
					this.queryInfo = res.queryInfo
					for (let i = 0; i < data.length; i++) {
						this.dataLists.push(data[i])
					}
					//console.log('this.queryInfo',this.queryInfo.page)
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
						//substr从0开始
						if(configCorp.length>3){
							if(configCorp.substr(0,1)=="1")
								this.canReturn=true
							if(configCorp.substr(1,1)=="0")
								this.showPrice=true
						}
						
					}
					uni.hideLoading();
				}catch(e){
					//
				}
				this.isLoading=false;
				this.loadStatus = 'loadmore';
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
					companyNo: this.userInfo.company_no,
					actType:2,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord,
					delLists:[],
					dataLists:[]
				}
				uni.showLoading({
					'title':'正在处理操作...'
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
				this.sn = e._id
				//this.$refs.form1.validateField('userInfo.sex')
			},
			styleSelect(e) {
				this.style = e._id
			},
			gxSelect(e) {
				this.gxName = e._id
			},
			saleNoSelect(e) {
				//console.log(e)
				this.saleNo = e._id
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
				if(this.showMoType)
					this.moType=item._id
				if(this.showSaleNo)
					this.saleNo=item._id
				if(this.showGx)
					this.gxName=item._id
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
	
</style>
