<template>
	<view class="container">
		<view class="header">
			<u-row customStyle="margin-bottom: 10px">
				<u-col span="11">
					<view class="u-demo-block__content">
						<u-tabs :list="tabLists" @change="changeTabsVal"></u-tabs>
					</view>
				</u-col>
				<u-col span="1">
					<view v-if="canEdit">
						<u-icon name="plus-circle-fill" color="#2979ff" size="24" @click="addItem(-1)"></u-icon>
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="body">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="1"><span class="text-no">{{index+1}}.</span></u-col>
							<u-col span="7">
								<u--input v-model="item.TName" border="none" placeholder="输入名称"></u--input>
							</u-col>
							<u-col span="2">
								<u--input type="number" v-model="item.TSort" border="none" placeholder="输入序号"></u--input>
							</u-col>
							<u-col span="2">
								<u-icon v-if="canEdit" name="trash-fill" color="#ff5500" size="24" @click="deleteItem(index)"></u-icon>
							</u-col>
						</u-row>
					</view>
					<view class="demo-layout" style="padding: 15px;">
						<u-button v-if="canEdit" @click="save(1)" type="primary">储存</u-button>
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
		saveParameter,
		saveParameterLists,
		dbInitParameter
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
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showFilter: false,
				
				type: 0,
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
				this.type = item.index
				this.keyword=''
				this.doSearch()
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
					ordBy: 'TSort',
					keyword:this.keyword
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				//注意get的参数 node 需要 {} ({params})
				let res = await getParameter({params});
				let data = res.dataLists
				this.queryInfo = res.queryInfo
				for (let i = 0; i < data.length; i++) {
					this.dataLists.push(data[i])
				}
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				if(this.type==0){
					this.tabLists=[{name:'全部'}];
					for(let i=0;i<this.dataLists.length;i++){
						this.tabLists.push({name:this.dataLists[i].TName})
					}
					if(this.dataLists.length==0 && !this.dbInit){
						//数据初始化
						this.dbInit=true
						await dbInitParameter({
							params: {}
						});
						this.doSearch()
					}
				}
				
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
			async save(actType){
				if(actType==4){
					this.showDel=true
					return
				}else{
					this.showDel=false
				}
				let params = {
					actType:actType,
					tid:this.tid,
					type:this.type,
					keyGuid:this.keyGuid,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:this.itemRecord,
					delLists:this.delLists,
					dataLists:this.dataLists,
				}
				
				uni.showLoading({
					'title':'正在处理操作...'
				});
				//注意提交的参数 node 无 {} 直接(params) PHP 需要({params})
				let res = await saveParameterLists(params);
				uni.hideLoading();
				console.log(params,res)
				//if(actType==2)
				//	this.dataLists.splice(this.itemIdx,1)
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
				}
				if (this.current == 2) {
					//this.dt2 = timeFormat(e.value, 'yyyy/mm/dd')
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
			snSelect(e) {
				this.sn = e.name
				//this.$refs.form1.validateField('userInfo.sex')
			},
			styleSelect(e) {
				this.style = e.name
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
