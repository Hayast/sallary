<template>
	<view class="container">
		<view>
			<view class="row-l-r " style="background-color: #F8F8FF;padding-top: 3px;">
				<view>
					<u-form labelAlign="right" labelPosition="left" :label-style="{'color':'blue','font-size':'1.1rem'}">
						<u-row>
							<u-col span="5">
								<u-form-item label="仓位:">
									<u--input @confirm="save(0)" :focus="ptFocus" @blur="ptFocus = false" confirm-type="done" v-model="ptNo" border="none" placeholder="输入仓位"></u--input>
								</u-form-item>
							</u-col>
							<u-col span="4">
								<u-form-item label="架号:" >
									<u--input  @confirm="save(1)" :focus="wbFocus" @blur="wbFocus = false" confirm-type="done" v-model="wbNo" border="none" placeholder="输入架号, 按回车提交"></u--input>
								</u-form-item>
							</u-col>
							<u-col span="3">
								<view style="display: inline-flex;text-align: center;">
									<view @click="save(1)">
										<u-icon name="minus-circle-fill" color="#2979ff" size="32"></u-icon>
									</view>
									<view @click="save(0)" style="padding-left: 8rpx;">
										<u-icon name="grid-fill" color="#2979ff" size="36"></u-icon>
									</view>
								</view>
							</u-col>
						</u-row>
						<u-row v-if="errMsg!=''" style="padding: 3px;">
							<u-col span="12">
								<view style="display: inline-flex;">
									{{errMsg}}
								</view>
							</u-col>
						</u-row>
						<u-row v-if="wbLists.length>0" style="padding: 3px;">
							<u-col span="12">
								<view style="display: inline-flex;">
									<view v-for="(item, index) in  wbLists" :key="index" style="margin-right: 10rpx;">
										<u-tag :text="item.TName" type="success" plain  @click="selectWb(item)"></u-tag>
									</view>
								</view>
							</u-col>
							
						</u-row>
					</u-form>
				</view>
				
			</view>
			
		</view>
		<view>
			<u-row customStyle="margin-bottom: 10px">
				<u-col span="12">
					<view class="u-demo-block__content">
						<u-tabs :list="ptFilters" @change="changeTabsVal"></u-tabs>
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="body">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="show-pt" @click="selectPt(item)">
						<view :class="item.TStatus==1?'show-pt-1':'show-pt-2'">
							<view>{{item.TName}}</view>
							<view>{{item.TUsed}} / {{item.TQuantity}}</view>
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
					</u-form>
				</view>
			</u-modal>
			<u-action-sheet :show="showTSn" :actions="whLists" title="请选择裁床号" description="..."
				@close="showTSn = false" @select="snSelect">
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
		getParameter,
		getWh,
		getPt,
		getWhio,
		saveWhio
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				errMsg:'',
				wbLists:[],
				ptFilters:[],
				ptFocus:false,
				wbFocus:false,
				ptNo:'',
				wbNo:'',
				labelWidth: 100,
				dbInit:false,
				canEdit:false,
				canApply:false,
				current: 0,
				tid: 0,
				keyGuid: '',
				showPage: 0,
				itemIdx: 0,
				itemRecord: {},
				keyword: '',
			
				showFilter: false,
				showDel: false,
				whLists: [{
						name: '000001',
					}
				],

				type: 0,
				whType:0,
				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				delLists: [],
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				inputStyle:'',
				customBtn:{
					color:'red',
					margin:'30rpx'
				},
				tabLists: [{
						name: '全部'
					}
				]

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
			this.canEdit=this.adminWrite[2];
			this.canApply=this.adminApply[2];
			this.getParameter(7,8);
			
			//this.getParameter(8);
			this.loadmore();
		},
		onReady() {
			// 微信小程序需要用此写法
			////this.$refs.dtP1.setFormatter(this.formatter)
			////this.$refs.dtP2.setFormatter(this.formatter)
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			changeTabsVal(item) {
				console.log(item.name,item.sort)
				this.type = item.index
				if(item.sort>=100){
					this.whType=0;
					this.keyword=item.name;
				}else{
					this.keyword='';
					this.whType=item.sort;
				}
				this.doSearch()
			},
			selectPt(item){
				this.errMsg='';
				this.ptNo=item.TName;
				//this.wbFocus=true;
				this.wbLists=item.TWbLists;
			},
			selectWb(item){
				this.errMsg='';
				this.wbNo=item.TName;
				//this.ptFocus=true;
			},
			reachBottom() {
				if (this.loadStatus == 'loading' || this.loadStatus == 'nomore') {
					return
				}
				this.loadStatus = "loading"
				setTimeout(() => {
					this.loadmore();
				}, 1200);

			},
			addItem(idx){
				
				this.dataLists.push({TGuid:'',TName:'',TSort:this.dataLists.length+1})
			},
			deleteItem(idx){
				this.delLists.push(this.dataLists[idx])
				this.dataLists.splice(idx,1)
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
				let params = {
					page: this.page,
					type: this.type,
					whType: this.whType,
					keyword:this.keyword
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				//注意get的参数 node 需要 {} ({params})
				let res = await getPt({params});
				let data = res.dataLists
				this.queryInfo = res.queryInfo
				for (let i = 0; i < data.length; i++) {
					this.dataLists.push(data[i])
				}
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.keyword=''
				this.showFilter=false
			},
			showMsg(msg) {
				uni.showToast({
					title: msg,
					icon: 'none'
				});
			},
			async save(actType){
				this.errMsg='';
				if(this.wbNo==''){
					this.errMsg='请输入架号.';
					this.showMsg(this.errMsg);
					return;
				}
				if(actType==0){
					if(this.ptNo==''){
						this.errMsg='请输入仓位编号.';
						this.showMsg(this.errMsg);
						return;
					}
				}
				let params = {
					actType,
					type:actType,
					uid:this.globalInfo.DefaultUser.uid,
					ptNo:this.ptNo,
					ptNo2:'',
					wbNo:this.wbNo
				}
				
				uni.showLoading({
					'title':'正在处理操作...'
				});
				//注意提交的参数 node 无 {} 直接(params) PHP 需要({params})
				let res = await saveWhio(params);
				uni.hideLoading();
				console.log(params,res)
				this.doSearch();
				this.wbLists=[];
				this.ptNo='';
				this.wbNo='';
				if(res.code!=0){
					this.errMsg=res.msg;
				}
			},
			async getParameter(type,type2=0) {
				let params = {
					page: 1,
					resType: 2,
					pageSize: 200,
					type: type,
					type2: type2,
					ordBy: 'TSort'
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				let res = await getParameter({
					params
				});
				uni.hideLoading();
				if (type == 7) {
					let data=[];
					this.ptFilters = []
					data.push({
						name: '全部',
						sort:0
					})
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						data.push({
							name: item.TName,
							sort:item.TSort
						})
					}
					this.ptFilters=data
					console.log('  this.ptFilters ==> ',this.ptFilters)
				}
				
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
				//
			},
			cancel(idx) {
				this.close()
			},
			confirm(e) {
				this.close()
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
			}
		}
	}
</script>

<style lang="scss">
	.show-pt{
		display: inline-block;
		margin:3rpx;
	}
	.show-pt-1 {
		border-radius: 20rpx;
		padding: 10rpx;
		text-align: center;
		width:120rpx;
		background-color: #FFD700;
	}
	.show-pt-2 {
		border-radius: 20rpx;
		padding: 10rpx;
		text-align: center;
		width:120rpx;
		background-color: #00FF7F;
	}
	.item-row {
		padding: 6rpx;
	}
</style>
