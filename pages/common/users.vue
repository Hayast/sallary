<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">帐号管理</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row"
						:class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="9">
								<view v-if="canEdit" @click="edit(item)">
									<span class="text-no">{{index+1}}.</span> {{item.nickname}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}.</span> {{item.nickname}}
								</view>
							</u-col>
							<u-col span="3">
								{{deptLists[0][item.dept||0].name || '未设置'}}
							</u-col>
						</u-row>
						<u-row>
							<u-col span="5">
								<span style="margin-left:10rpx;">
									{{item.mobile || ''}}
								</span>
							</u-col>
							<u-col span="3">
								{{item.TNum1 || ''}}
							</u-col>
							<u-col span="3">
								{{typeLists[0][item.type||0].name || ''}}
							</u-col>
							<u-col span="1" @click="edit(item)">
								<u-icon v-if="canEdit" name="edit-pen-fill" color="#2979ff" size="24"></u-icon>
							</u-col>
						</u-row>

					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				<view class="page-box" v-if="showPage==1">
					<view>
						<u--form>
							<u-form-item label="姓名:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.nickname" border="none" placeholder="输入姓名"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="手机:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.mobile" border="none" placeholder="输入手机"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="底薪:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TNum1" border="none" placeholder="输入底薪"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item :labelWidth="labelWidth" label="部门" borderBottom
								@click="showDept=true;showActType=0; hideKeyboard()">
								{{deptLists[0][itemRecord.dept].name}}
								<u-icon slot="right" name="arrow-right"></u-icon>
							</u-form-item>
							<u-form-item :labelWidth="labelWidth" label="类型" borderBottom
								@click="showType=true;showActType=0; hideKeyboard()">
								{{typeLists[0][itemRecord.type].name}}
								<u-icon slot="right" name="arrow-right"></u-icon>
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
				:show-confirm-button="true" @cancel="cancelDelete" @confirm="save(2)" ref="uModal" :async-close="false"
				cancel-text="取消" confirm-text="确认删除">
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
						<u-form-item :labelWidth="labelWidth" label="部门" borderBottom
							@click="showDept=true;showActType=1; hideKeyboard()">
							<u--input v-model="deptName" disabled disabledColor="#ffffff" placeholder="请选择部门"
								border="none">
							</u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="类型" borderBottom
							@click="showType=true;showActType=1; hideKeyboard()">
							<u--input v-model="typeName" disabled disabledColor="#ffffff" placeholder="请选择类型"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>

					</u-form>
				</view>
			</u-modal>
			<u-picker
				:show="showDept"
				:columns="deptLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showType"
				:columns="typeLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			
		</view>
		<view v-if="isShow">
            <page-container :show="isShow" :overlay="false" @beforeleave="beforeleave"></page-container>
        </view>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				isShow:true,
				labelWidth: 100,
				current: 0,
				canEdit: false,
				canApply: false,
				userType:0,
				showPage: 0,
				showActType: 0,
				itemIdx: 0,
				dept: -1,
				deptName: '',
				type: -1,
				typeName: '',
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
				showDept: false,
				showType: false,
				sn: '',
				style: '',
				deptLists: [{
					name: '',
					sort: 0
				}],
				typeLists: [[{
						name: '超级管理员',
						value: 0
					},
					{
						name: '审核',
						value: 1
					},
					{
						name: '会计',
						value: 2
					},
					{
						name: '主管',
						value: 3
					},
					{
						name: '正式员工',
						value: 4
					},
					{
						name: '试用员工',
						value: 5
					},
					{
						name: '待验证',
						value: 6
					},
					{
						name: '禁用',
						value: 7
					}
				]],

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputType:'backgroundColor:#fff;margin:4rpx;'
				inputType: '',
				customBtn: {
					color: 'red',
					margin: '30rpx'
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
			this.canEdit=this.adminWrite[0];
			this.canApply=this.adminApply[0];
			this.getParameter(1)
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
			beforeleave(){
				if(this.showPage==1){
					this.isShow = false;  //这个很重要，一定要先把弹框删除掉
					uni.showModal({
						title: "确定要取消储存并离开吗?",
						success: (e)=>{
							if(e.confirm) {
								//判断是上一个页面进入(返回)，还是直接进入这个页面(回首页)
								this.showPage=0;
							}else {
								//点取消，生成新的弹框
								this.showPage=1;
							}
							this.isShow = true;
						}
					})
				}
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
			doSearch() {
				this.page = 1
				this.showFilter = false
				this.dataLists = []
				this.loadmore()
			},
			edit(item, itemIdx) {
				this.itemIdx = itemIdx
				this.itemRecord = item
				this.showPage = 1
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let params = {
					page: this.page,
					action: 'getUser',
					userType:this.userType,
					//tokenYun:this.userInfo.token,
					userType: 0,
					dept: this.dept,
					type: this.type,
					keyword: this.keyword
				}
				console.log(params)
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//post wu {}
				let res = await astCf(params);
				let data = res.dataLists
				if(!data){
					uni.hideLoading();
					uni.showToast({
						title:'出错了...'
					})
					return;
				}
				this.queryInfo = res.queryInfo
				data.forEach(item => {
					this.dataLists.push(item)
				});
				// for (let i = 0; i < data.length; i++) {
				// 	this.dataLists.push(data[i])
				// }
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				
				if (this.queryInfo.isLast) {
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter() {
				this.dept = ''
				this.type = -1
				this.showFilter = false
			},
			async save(actType) {
				if (actType == 4) {
					this.showDel = true
					return
				} else {
					this.showDel = false
				}
				
				let params = {
					actType: actType,
					uid: this.globalInfo.DefaultUser.uid,
					//tokenYun:this.userInfo.token,
					userType:this.userType,
					action:'updateUser',
					itemRecord: this.itemRecord
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				//post wu {}
				let res = await astCf(params);
				uni.hideLoading();
				if (actType == 2)
					this.dataLists.splice(this.itemIdx, 1)
				//this.showDel=false
				this.showPage = 0
			},
			async configDelete() {
				this.save(2)
			},
			cancelDelete() {
				this.showDel = false
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
			
			async getParameter(type) {
				let params = {
					page: 1,
					resType: 0,
					pageSize: 200,
					type: type,
					ordBy: 'TSort'
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				let res = await getParameter({
					params
				});
				//let data = res.dataLists
				console.log(type,res)
				uni.hideLoading();
				if (type == 1) {
					this.deptLists = []
					let data=[{
						name: '请选择',
						sort: 0
					}]
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						data.push({
							name: item.TName,
							sort: item.TSort
						})
					}
					this.deptLists.push(data)
					console.log(this.deptLists)
				}
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},		
			selectPicker(e) {
				let item = e.value[0];
				if(this.showDept){
					if(this.showActType==1){
						this.dept = item.sort
						this.deptName = item.name
					}else{
						this.itemRecord.dept=item.sort
					}
				}
				if(this.showType){
					if(this.showActType==1){
						this.type = item.value
						this.typeName = item.name
					}else{
						this.itemRecord.type=item.value
					}
				}
				this.cancelPicker();
			},
			confirmPicker(e) {
				this.selectPicker(e)
			},
			cancelPicker() {
				this.showDept = false
				this.showType = false
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
