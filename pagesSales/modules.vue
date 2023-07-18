<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">样品管理</view>
				<view v-if="showPage==0" style="width:160upx;">
						<view style="width:80upx;float: left;">
							<u-icon name="search" color="#0989de" size="36" @click="showFilter=true"></u-icon>
						</view>
						<view style="width:80upx;">
							<u-icon v-if="canEdit" name="plus-circle-fill" color="#2775f3" size="36" @click="add()"></u-icon>
						</view>
				</view>
				<view v-if="showPage==1">
					<u-icon name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view v-if="showPage==0" scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row"
						:class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="9">
								<view v-if="canEdit" @click="edit(item)">
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TCustName}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TCustName}}
								</view>
							</u-col>
							<u-col span="3">
								{{item.TMo || ''}}
							</u-col>
						</u-row>
						<u-row>
							<u-col span="5">
								<span style="margin-left:10rpx;">
									{{item.TMo || ''}}
								</span>
							</u-col>
							<u-col span="6" @click="edit(item)">
								{{item.TModule || ''}}
							</u-col>
							<u-col span="1" @click="edit(item)">
								<u-icon name="edit-pen-fill" color="#2979ff" size="24"></u-icon>
							</u-col>
						</u-row>

					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				
			</scroll-view>
			<view class="page-box" v-if="showPage==1">
				<view>
					<u--form>
						<u-form-item label="编号:" :label-width="labelWidth" borderBottom>
							<view>{{itemRecord.TGuid}}</view>
						</u-form-item>
						<u-form-item label="客户:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TCustName" border="none" placeholder="输入客户"
								:customType="inputType"></u--input>
						</u-form-item>
						
						<u-form-item label="客户编号:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TCusTGuid" border="none" placeholder="输入客户编号"
								:customType="inputType"></u--input>
						</u-form-item>
						<u-form-item label="本厂编号:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TMo" border="none" placeholder="输入本厂编号"
								:customType="inputType"></u--input>
						</u-form-item>
						<u-form-item label="制作方法:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TModule" border="none" placeholder="输入制作方法"
								:customType="inputType"></u--input>
						</u-form-item>
						<view v-for="(item, index) in itemRecord.TArrA" :key="index">
							<u-divider :text="arrAText[index]"></u-divider>
							<u-form-item label="物料名称:" :label-width="labelWidth" borderBottom>
								<u--input v-model="item.TName" border="none" placeholder="输入物料名称"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="模具:" :label-width="labelWidth" borderBottom>
								<u--input v-model="item.TA1" border="none" placeholder="输入模具"
									:customType="inputType"></u--input>
							</u-form-item>
							<u-form-item label="上模温度:" :label-width="labelWidth" borderBottom>
								<u--input v-model="item.TA2" border="none" placeholder="输入上模温度"
									:customType="inputType"></u--input>
									<u-button slot="right" type="default" size="mini">°C</u-button>
							</u-form-item>
							<u-form-item label="下模温度:" :label-width="labelWidth" borderBottom>
								<u--input v-model="item.TA3" border="none" placeholder="输入下模温度"
									:customType="inputType"></u--input>
									<u-button slot="right" type="default" size="mini">°C</u-button>
							</u-form-item>
							<u-form-item label="时间:" :label-width="labelWidth" borderBottom>
								<u--input v-model="item.TA4" border="none" placeholder="输入时间"
									:customType="inputType"></u--input>
								<u-button slot="right" type="default" size="mini">.S</u-button>
							</u-form-item>
						</view>
						<scroll-view v-if="false" scroll-x="true" class="scroll-view">
							<view class="item1">
								ssssss
							</view>
							<view v-if="false" class="item2">
								xxxxxxxxxx
							</view>
							<view v-if="false" class="item3">
								XXXXXXXXXX
							</view>
						</scroll-view>
						<scroll-view scroll-x="true" class="scroll-view">
							<view class="item-m">
								<view class="v-w-1200" v-for="(list, index) in itemRecord.TArrB" :key="index" :class="index%2==0?'bg-c1':'bg-c2'">
									<u-row>
										<u-col span="2">{{arrBText[index]}}</u-col>
										<u-col span="1">{{list.A1 || ''}}</u-col>
										<u-col span="1">{{list.A2 || ''}}</u-col>
										<u-col span="1">{{list.A3 || ''}}</u-col>
										<u-col span="1">{{list.A4 || ''}}</u-col>
										<u-col span="1">{{list.A5 || ''}}</u-col>
										<u-col span="1">{{list.A6 || ''}}</u-col>
										<u-col span="1">{{list.A7 || ''}}</u-col>
										<u-col span="1">{{list.A8 || ''}}</u-col>
										<u-col span="1">{{list.A9 || ''}}</u-col>
										<u-col span="1">{{list.A10 || ''}}</u-col>
									</u-row>
								</view>
							</view>
							
						</scroll-view>
					</u--form>
				</view>
				<view>
					<view class="custom-btn">
						<u-button v-if="canEdit" type="primary"  @click="save(actType)">储存</u-button>
					</view>
					<view v-if="itemRecord._id!=''" class="custom-btn">
						<u-button v-if="canEdit" type="warning" :class="custom-btn" @click="save(4)">删除</u-button>
					</view>
				</view>
			
			</view>

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
				:show="showSize"
				:columns="sizeLists"
				keyName="name"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			<u-picker
				:show="showColor"
				:columns="colorLists"
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
			<u-picker
				:show="showUnit"
				:columns="unitLists"
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
		saveModules,
		getModules,
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
				arrAText:['棉料','面布','隔层布'],
				arrBText:['尺码','客人码','A(上碗)','B(下碗)','C(夹位)','D(杯高)','E(杯阔)','F(耳仔)','G(压边)','H','I','J','K','棉杯尺寸','棉杯对/码','','面布尺寸','面布对/码','','隔层布尺寸','隔层布对/码'],
				labelWidth: 100,
				current: 0,
				canEdit: false,
				canApply: false,
				userType:0,
				showPage: 0,
				showActType: 0,
				itemIdx: 0,
				dept: 0,
				deptName: '',
				type: 0,
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
				showSize: false,
				showColor: false,
				showType: false,
				showUnit: false,
				sn: '',
				style: '',
				sizeLists: [{
					name: '请选择',
					sort: 0
				}],
				colorLists: [{
					name: '请选择',
					sort: 0
				}],
				
				typeLists: [[{
						name: '请选择',
						value: 0
					}
				]],
				unitLists: [[{
						name: '请选择',
						value: 0
					},{
						name: '件',
						value: 0
					},{
						name: '个',
						value: 0
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
			let idxAdmin=20;
			this.canEdit=this.adminWrite[idxAdmin];
			this.canApply=this.adminApply[idxAdmin];
			
			this.getParameter(2)
			this.getParameter(3)
			this.getParameter(9)
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
			add(){
				this.actType=0;
				this.itemIdx = this.dataLists.length
				this.itemRecord = {_id:'',TName:'',TType:0,TPrice:0,TColor:'',TSize:'',TUnit:''}
				this.showPage = 1
			},
			edit(item, itemIdx) {
				this.actType=1;
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
					companyNo: this.userInfo.company_no,
					//tokenYun:this.userInfo.token,
					type: this.type,
					showDel: 0,
					keyword: this.keyword
				}
				console.log(params)
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//post wu {}
				let res = await getModules({params});
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
					companyNo: this.userInfo.company_no,
					actType: actType,
					uid: this.globalInfo.DefaultUser.uid,
					//tokenYun:this.userInfo.token,
					itemRecord: this.itemRecord
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				let res = await saveModules(params);
				uni.hideLoading();
				if (actType == 2)
					this.dataLists.splice(this.itemIdx, 1)
				if (actType == 0)
					this.doSearch();
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
					companyNo: this.userInfo.company_no,
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
				if (type == 2) {
					this.colorLists = []
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
					this.colorLists.push(data)
				}
				if (type == 3) {
					this.sizeLists = []
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
					this.sizeLists.push(data)
				}
				if (type == 9) {
					this.typeLists = []
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
					this.typeLists.push(data)
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
				if(this.showSize){
					this.itemRecord.TSize=item.name
				}
				if(this.showColor){
						this.itemRecord.TColor=item.name
				}
				if(this.showUnit){
					this.itemRecord.TUnit=item.name
				}
				if(this.showType){
					if(this.showActType==1){//搜索
						this.type = item.sort
						this.typeName = item.name
					}else{
						this.itemRecord.TType=item.sort
						console.log(this.itemRecord.TType,this.typeLists[0][this.itemRecord.TType])
					}
				}
				this.cancelPicker();
			},
			confirmPicker(e) {
				this.selectPicker(e)
			},
			cancelPicker() {
				this.showSize = false
				this.showType = false
				this.showColor = false
				this.showUnit = false
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
	.s-v-x{
		white-space:nowrap;
		min-width: 800upx;
	}
	.c-w-min{
		min-width: 100upx;
	}
	.scroll-view{
		width: 100%;
		white-space: nowrap;
	}
	.item-m{
		display: inline-block;
		min-width: 1600upx;
		background-color: #f7f7f7;
	}
	.v-w-1200{
		min-width: 1600upx;
	}
	
	.bg-c1{
		background-color: #f9f9f9;
	}
	.bg-c2{
		background-color: #fcfcfc;
	}
	.item1,.item2,.item3{
		display: inline-block;
		width: 100%;
		height: 100%;
	}
	.item1{
		background-color: #007AFF;
	}
	.item2{
		background-color: #333333;
	}
	.item3{
		background-color: #4CD964;
	}
</style>
