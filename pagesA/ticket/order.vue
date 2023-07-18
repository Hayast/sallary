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
					￥:
				<span class="font-red">
					{{FormatNum(queryInfo.num2,2)}}
				</span>
			</view>
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box">
					<view class="item-row" :class="index%2==0?'bg-1':'bg-2'" v-for="(item, index) in dataLists" :key="index">
						<u-row>
							<u-col :span="10">
								<view v-if="canEdit"  @click="edit(1,item,index)">
									<span class="text-no">{{index+1}}. </span>[{{item.TNo||''}}-{{item.TNum1||''}}]{{item.TSaleNo||''}}({{item.TType||''}}){{item.TName||''}}{{item.TSkuName||''}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}. </span>[{{item.TNo||''}}-{{item.TNum1||''}}]{{item.TSaleNo||''}}({{item.TType||''}}){{item.TName||''}}{{item.TSkuName||''}}
								</view>
							</u-col>
							<u-col :span="2">{{item.TEmpName||''}}</u-col>
						</u-row>
						<u-row>
							<u-col :span="7">
								<view>
									<span class="price-1">¥:</span>
									<span v-if="item.TQuantity<0">
										<span style="color:red;">{{FormatNum(item.TQuantity)}}</span>*{{FormatNum(item.TPrice)}}=<span style="color:red;">{{FormatNum(item.TAmt)}}</span>
									</span>
									<span v-if="item.TQuantity>0">
									{{FormatNum(item.TQuantity)}}*{{FormatNum(item.TPrice)}}={{FormatNum(item.TAmt)}}
									</span>
								</view>
							</u-col>
							<u-col :span="3"><span class="price-date">{{item.TDt.substr(5,11)}}</span></u-col>
							<u-col v-if="canEdit" :span="1" @click="edit(1,item,index)">
								<u-icon v-if="item.TStatus<1" name="edit-pen-fill" color="#0055ff" size="24"></u-icon>
							</u-col>
							<u-col v-if="canEdit" :span="1" @click="edit(4,item,index)">
								<u-icon v-if="item.TStatus<1" name="trash-fill" color="#ffaa00" size="24"></u-icon>
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
		
		<u-modal :show="showEdit" title="编辑数量" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="showEdit=false;" @confirm="save()" ref="uModal"
			:async-close="false" cancel-text="关闭" confirm-text="储存">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<u-form ref="uForm">
					<u-form-item label="编号:" :label-width="70" borderBottom>
						{{itemRecord.TNo}} 
					</u-form-item>
					<u-form-item label="名称:" :label-width="70" borderBottom>
						 {{itemRecord.TName}}
					</u-form-item>
					<u-form-item label="数量:" :label-width="70" borderBottom>
						<u--input type="number" v-model="itemRecord.TQuantity" border="none" placeholder="数量"></u--input>
					</u-form-item>
				</u-form>
			</view>
		</u-modal>
		
		<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
			:async-close="false" cancel-text="重设" confirm-text="搜索">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
						<u-input type="number" v-model="keyword" />
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
					<u-form-item :labelWidth="labelWidth" label="员工" borderBottom
						@click="showEmp=true; hideKeyboard()">
						<u--input v-model="empName" disabled disabledColor="#ffffff" placeholder="请选择员工"
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
			:show="showGx"
			:columns="gongxuLists"
			keyName="_id"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showEmp"
			:columns="[empLists]"
			keyName="nickname"
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

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
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
				canEdit: false,
				changedDt: false,
				canApply: false,
				showEdit: false,
				itemIdx: 0,
				showScroll: 0,
				itemRecord:{},
				keyword: '',
				unit1:'',
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
				showGx: false,
				showEmp: false,
				moType:'',
				moTypeLists:[],
				showMoType:false,
				sn: '',
				emp: '',
				empName: '',
				style: '',
				showSaleNo: false,
				gongxuLists:[{_id:''}],
				saleNoLists:[{_id:''}],
				saleNo: '',
				snLists: [{
						_id: '000001',
					}
				],
				styleLists: [{
						_id: '01',
					}
				],
				gxName:'',
				gongxuLists: [{
						_id: '01',
					}
				],
				empLists:[],

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
				queryInfo: {dt1:timeFormat(new Date(), 'yyyy/mm/dd'),dt2:timeFormat(new Date(), 'yyyy/mm/dd'),num1:0,num2:0,isLast:false,page:1}

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
		onLoad(option) {
			console.log(option)
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
			this.canEdit=this.adminWrite[5];
			this.canApply=this.adminApply[5];
			this.textDt1= this.now()
			this.textDt2= this.now()
			this.changedDt=true;
			this.loadmore();
			this.loadEmp();
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
				if(this.queryInfo.isLast)
					return
				let actDt=0;
				if(this.changedDt)
					actDt=1;
				this.changedDt=false;
				let params = {
					companyNo: this.userInfo.company_no,
					actType:11,
					page: this.page,
					showDel:0,
					actDt,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					keyword:this.keyword,
					dtIdx: this.dtIdx,
					sn: this.sn||'',
					uid: this.emp||'',
					style: this.style||'',
					gongxu: this.gxName||'',
					moType: this.moType||'',
					saleNo: this.saleNo||''
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
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
					//this.saleLists=[]
					//this.saleLists.push(this.queryInfo.saleLists)
					this.gongxuLists=[]
					this.gongxuLists.push(this.queryInfo.gongxuLists)
					this.moTypeLists=[]
					this.moTypeLists.push(this.queryInfo.moTypeLists)
					if(actDt==1){
						this.saleNoLists=[]
						this.saleNoLists.push(this.queryInfo.saleLists)
					}
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			async loadEmp() {
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					pageSize:500,
					action: 'getUser',
					userType:0,
					tokenYun:'',
					userType: 0
				}
				let res = await astCf(params);
				this.empLists = res.dataLists;
				//console.log(this.empLists)
			},
			edit(actType,item,itemIdx){
				this.itemRecord=item
				this.itemIdx=itemIdx
				if(actType==1){
					this.showEdit=true;
					return;
				}
				if(actType==4){
					this.showDel=true
					return
				}
				//this.actType=actType
			},
			async save(){
				this.itemRecord.TAmt=parseInt(this.itemRecord.TQuantity)*parseFloat(this.itemRecord.TPrice);
				let params = {
					companyNo: this.userInfo.company_no,
					actType:1,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord,
					delLists:[],
					dataLists:[]
				}
				uni.showLoading({
					'title':'正在处理操作...'
				});
				let res = await saveOrder(params);
				uni.hideLoading();
				this.showEdit=false
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
				this.dtIdx=idx;
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
				this.doSearch()
			},
			cancelFilter(){
				this.keyword=''
				this.sn=''
				this.style=''
				this.gxName=''
				this.saleNo=''
				this.emp=''
				this.empName=''
				this.moType=''
				this.showFilter=false
				this.showEdit=false
				this.showSaleNo=false
				this.showMoType=false
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
			
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},			
			selectPicker(e) {
				let item = e.value[0];
				console.log(item)
				if(this.showSn)
					this.sn=item._id
				if(this.showStyle)
					this.style=item._id
				if(this.showGx)
					this.gxName=item._id
				if(this.showEmp){
					this.emp=item._id
					this.empName=item.nickname
				}
				if(this.showMoType){
					this.moType=item._id
				}
				if(this.showSaleNo)
					this.saleNo=item._id
				this.cancelPicker();
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.cancelPicker()
			},
			cancelPicker() {
				this.showSn = false
				this.showStyle = false
				this.showGx = false
				this.showSaleNo = false
				this.showEmp = false
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
	
</style>
