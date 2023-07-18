<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">结算工资</view>
				<view>
					<u-icon v-if="isAdmin && showPage==0 && canEdit" name="more-dot-fill" color="#55aaff" size="24" @click="showManage=true"></u-icon>
				</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
			
		</view>
		<view class="body u-page__item">
			<view>
				<span v-if="false">
					日期由 {{queryInfo.dt1.substr(5,10)}}至{{queryInfo.dt2.substr(5,10)}}
				</span>
				金额:
				<span class="font-red">
					{{FormatNum(queryInfo.num2,0)}}
				</span> 
				
			</view>
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="12">
								<view>
									<span class="text-no">{{index+1}}.</span> {{item.TNo}}-{{item.TName}}
								</view>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="2">
								{{FormatNum(item.TNum1,0) || ''}}
							</u-col>
							<u-col span="2">
								{{FormatNum(item.TNum2,0) || ''}}
							</u-col>
							<u-col span="2">
								{{FormatNum(item.TNum3,0) || ''}}
							</u-col>
							<u-col span="2">
								{{FormatNum(item.TNum8,0) || ''}}
							</u-col>
							<u-col span="3">
								{{FormatNum(item.TAmt,0) || ''}}
							</u-col>
							<u-col span="1" v-if="isAdmin && canEdit">
								<u-icon name="edit-pen-fill" color="#2979ff" size="24" @click="edit(item)">
								</u-icon>
							</u-col>
						</u-row>

					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				<view class="page-box" v-if="showPage==1">
					<view>
						<u--form>
							<u-form-item label="编号:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TNo}}
							</u-form-item>
							<u-form-item label="姓名:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TName" border="none" placeholder="输入姓名" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="底薪:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TNum1" border="none" placeholder="输入底薪" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="件薪:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TNum2" border="none" placeholder="输入件薪" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="补贴:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TNum3" border="none" placeholder="输入补贴" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="扣款:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TNum8" border="none" placeholder="输入扣款" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item label="应发:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TAmt" border="none" placeholder="输入应发" :customStyle="inputStyle"></u--input>
							</u-form-item>
						</u--form>
					</view>
					<view>
						<view class="custom-btn">
							<u-button type="primary"  @click="save(1)">储存</u-button>
						</view>
						<view class="custom-btn">
							<u-button type="warning" :class="custom-btn" @click="save(4)">删除</u-button>
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

			<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
				:async-close="false" cancel-text="重设" confirm-text="搜索">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
							<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
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
			<u-modal :show="showManage" title="生成工资" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelManage" @confirm="createSallary()" ref="uModal"
				:async-close="false" cancel-text="取消" confirm-text="确认">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="labelWidth" label="年" borderBottom
							@click="showYear=true; hideKeyboard()">
							<u--input v-model="year" disabled disabledColor="#ffffff" placeholder="请选择年" border="none">
							</u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="月" borderBottom
							@click="showMonth=true; hideKeyboard()">
							<u--input v-model="month" disabled disabledColor="#ffffff" placeholder="请选择月"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
					</u-form>
				</view>
			</u-modal>
			<u-action-sheet :show="showYear" :actions="yearLists" title="请选择年" description="..."
				@close="showYear = false" @select="yearSelect">
			</u-action-sheet>
			<u-action-sheet :show="showMonth" :actions="monthLists" title="请选择月" description="..."
				@close="showMonth = false" @select="monthSelect">
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
		getSallary,
		saveSallary,
		createSallary
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
				isAdmin:false,
				canEdit: false,
				canApply: false,
				showManage:false,
				showPage: 0,
				itemIdx: 0,
				itemRecord: {},
				keyword: '',
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
				showYear: false,
				showMonth: false,
				year: '',
				month: '',
				yearLists: [],
				monthLists: [],

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				queryInfo: {dt1:'',dt2:'',num1:0,num2:0},
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
		onLoad(option) {
			if(option.admin==1){
				this.isAdmin=true
				console.log('========isadmin==============')
			}
			this.canEdit=this.adminWrite[4];
			this.canApply=this.adminApply[4];
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
			let dt=new Date()
			let year=dt.getFullYear()
			for(let i=year-3;i<=year;i++){
				this.yearLists.push({name:i})
			}
			this.year=year
			let month=dt.getMonth()+1
			this.month=month<10?'0'+month:month
			
			for(let i=1;i<13;i++){
				if(i<10){
					this.monthLists.push({name:'0'+i})
				}
				else
				{
					this.monthLists.push({name:i})
				}
			}
			this.textDt1=this.getDtYm(this.textDt1);
			this.textDt2=this.getDtYm(this.textDt2);
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
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let dt1=this.getDtYm(this.textDt1);
				let dt2=this.getDtYm(this.textDt2);
				let iAdmin=0;
				if(this.isAdmin)
					iAdmin=1;
				let params = {
					companyNo: this.userInfo.company_no,
					page: this.page,
					showDel:0,
					dt1: dt1,
					dt2: dt2,
					iAdmin:iAdmin,
					keyword:this.keyword
				}
				console.log(params)
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getSallary({
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
					this.yearLists=this.queryInfo.yearLists
					this.monthLists=this.queryInfo.monthLists
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			cancelManage(){
				this.showManage=false
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
			},
			async save(actType){
				if(actType==4){
					this.showDel=true
					return
				}else{
					this.showDel=false
				}
				let params = {
					companyNo: this.userInfo.company_no,
					actType:actType,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord
				}
				uni.showLoading({
					'title':'正在处理操作...'
				});
				let res = await saveSallary(params);
				uni.hideLoading();
				if(actType==2)
					this.dataLists.splice(this.itemIdx,1)
				//this.showDel=false
				this.showPage=0
			},
			async createSallary(){
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					page:1,
					pageSize:100,
					uid:this.globalInfo.DefaultUser.uid,
					year:this.year,
					month:this.month					
				}
				console.log(params)
				uni.showLoading({
					'title':'正在处理操作...'
				});
				let res = await createSallary(params);
				uni.hideLoading();
				this.showManage=false;
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
					this.textDt1 = timeFormat(e.value, 'yyyymm')
				}
				if (this.current == 2) {
					//this.dt2 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt2 = timeFormat(e.value, 'yyyymm')
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
			yearSelect(e) {
				this.year = e.name
				//this.$refs.form1.validateField('userInfo.sex')
			},
			monthSelect(e) {
				this.month = e.name
			},
			hideKeyboard() {
				uni.hideKeyboard()
			}
		}
	}
</script>

<style lang="scss">
	.item-row {
		padding: 6rpx;
	}
</style>
