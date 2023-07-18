<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">订单管理</view>
				<view v-if="showPage==0">
					<u-icon name="search" color="#0989de" size="36" @click="showFilter=true"></u-icon>
				</view>
				<view v-if="showPage==1">
					<u-icon name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row"
						:class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="9">
								<view>
									<span class="text-no">[{{index+1}}].</span>{{item.TGuid}} {{item.TCorpName||''}} {{item.TPhone || ''}}
								</view>
							</u-col>
							<u-col span="3">
								{{item.TDt.substring(5,11) || ''}}
							</u-col>
						</u-row>
						<u-row>
							<u-col span="5">
								<span style="margin-left:10rpx;">
									{{item.TCorpName || ''}}
								</span>
							</u-col>
							<u-col span="6">
								{{item.TAddress||''}}
							</u-col>
							
							<u-col span="1" @click="edit(item)">
								<u-icon v-if="item.TType==0" name="trash-fill" color="#2979ff" size="24"></u-icon>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="12">
								<view v-for="(goods, index2) in item.TDetailLists" :key="index2"  class="item" :class="index2%2==0?'bg-1':'bg-2'">
										<image mode="aspectFill" :src="globalInfo.Domain+'/'+goods.TPhoto" />
										<div class="grid">
										  <div>{{ goods.TName }}</div>
										  <div> {{ goods.TSize }}</div>
										  <div>{{ goods.TPrice }} * {{ goods.TQuantity }} = ￥ {{ goods.TAmt }}</div>
										</div>
								</view>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="4">
								<span style="margin-left:5rpx;">
								</span>
							</u-col>
							<u-col span="4">
								<span style="margin-left:5rpx;">
									总数量: {{item.TQuantity || ''}}
								</span>
							</u-col>
							<u-col span="4">
								金额: {{item.TAmt||''}}
							</u-col>
						</u-row>
					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
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
				:show="showCorp"
				:columns="[corpLists]"
				keyName="nickname"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
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
		savePio,
		getPio,
		getProductItem,
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
				keyword_corp:'',
				keyword_item:'',
				showFilterCorp:false,
				showFilterItem:false,
				ioType:1,
				ioTypes:['产品入库','产品出库'],
				labelWidth: 100,
				current: 0,
				canEdit: false,
				canApply: false,
				userType:0,
				showPage: 0,
				showActType: 0,
				showCorp: false,
				corpLists:[],
				itemLists:[],
				itemIdx: 0,
				dept: 0,
				deptName: '',
				type: 0,
				typeName: '',
				itemRecord: {},
				keyword: '',
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showSize: false,
				showType: false,
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
		onLoad(options) {
			//this.ioType=options.type;
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
			let idxAdmin=parseInt(this.ioType)+14;
			this.canEdit=this.adminWrite[idxAdmin];
			this.canApply=this.adminApply[idxAdmin];
			this.getParameter(2)
			this.getParameter(3)
			this.getParameter(5)
			this.loadmore();
			this.doSearchCorp();
			this.doSearchItem();
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

			reachBottom() {
				if (this.loadStatus == 'loading' || this.loadStatus == 'nomore') {
					return
				}
				this.loadStatus = "loading"
				setTimeout(() => {
					this.loadmore();
				}, 1200);

			},
			async doSearchCorp() {
				let userType=1;
				if(this.ioType==0)
					userType=2;
				let params = {
					page: 1,
					companyNo: this.userInfo.company_no,
					action: 'getUser',
					userType,
					//tokenYun:this.userInfo.token,
					keyword: this.keyword_corp
				}
				let res = await astCf(params);
				if(res.dataLists){
					this.corpLists=res.dataLists;
				}
				
			},
			async doSearchItem() {
				console.log(this.keyword_item)
				let params = {
					page: 1,
					companyNo: this.userInfo.company_no,
					//tokenYun:this.userInfo.token,
					showDel:0,
					actType: this.type,
					type:this.type,
					ioType:this.ioType,
					keyword: this.keyword_item
				}
				
				let res = await getProductItem({params});
				if(res)
					this.itemLists=res.dataLists;
			},
			selectItem(item){
				this.showFilterItem=false;
			},
			selectCorp(item){
				this.itemRecord.TCorpGuid=item.guid;
				this.itemRecord.TCorpName=item.nickname;
				if(item.AddrLists){
					if(item.AddrLists.length>0){
						this.itemRecord.TPhone=item.AddrLists[0].TPhone;
						this.itemRecord.TAddress=item.AddrLists[0].TAddress;
					}	
				}
					
				this.showFilterCorp=false;
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
				this.itemRecord = {_id:'',TGuid:'',TType:0,TName:'',TStyle:'',TUnit:'',TQuantity:0,TDetailLists:[]}
				this.showPage = 1
			},
			edit(item, itemIdx) {
				this.actType=1;
				this.itemIdx = itemIdx
				this.itemRecord = item
				this.showPage = 1
				if(this.itemRecord.TDetailLists==undefined)
					this.itemRecord.TDetailLists=[];
			},
			addItem(idx){
				let listT=[]
				let k=0;
				let len=this.itemRecord.TDetailLists.length
				if(len==0){
					listT.push({TGuid:'',TPrice:0,TColor:'',TSize:'',TQuantity:0})
				}else{
					for(let i=0;i<len;i++){
						k=k+1
						listT.push(this.itemRecord.TDetailLists[i])
						if(i==idx){
							k=k+1
							listT.push({TGuid:'',TPrice:0,TColor:'',TSize:'',TQuantity:0})
						}
					}
				}
				
				this.itemRecord.TDetailLists=listT
			},
			addItemData(item){
				let itemT={TGuid:'',TProductGuid:item.TGuid,TGuid:item.TGuid,TName:item.TName,TPrice:item.TPrice||'',TColor:item.TColor,TSize:item.TSize,TQuantity:1}
				this.itemRecord.TDetailLists.push(itemT)
				this.showFilterItem=false
			},
			deleteItem(idx){
				//this.delLists.push(this.itemRecord.TDetailLists[idx])
				this.itemRecord.TDetailLists.splice(idx,1)
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let params = {
					page: this.page,
					companyNo: this.userInfo.company_no,
					showDel:0,
					actType: 3,
					type: this.type,
					ioType:this.ioType,//2 查本人出库
					keyword: this.keyword,
					dt1:this.textDt1.replace(/\//g, "-"),
					dt2:this.textDt2.replace(/\//g, "-")
				}
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//post wu {}
				let res = await getPio({params});
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
				this.showFilterItem = false
				this.showFilterCorp = false
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
					companyNo: this.userInfo.company_no,
					ioType:this.ioType,
					uid: this.globalInfo.DefaultUser.uid,
					//tokenYun:this.userInfo.token,
					itemRecord: this.itemRecord
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				let res = await savePio(params);
				console.log(res,params)
				uni.hideLoading();
				if (actType == 2)
					this.dataLists.splice(this.itemIdx, 1)
				if (actType == 0)
					this.doSearch()
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
					companyNo: this.userInfo.company_no,
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
				if (type == 5) {
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
				//console.log(item)
				if(this.showCorp){
					this.selectCorp(item)
				}
				if(this.showSize){
					if(this.showActType==1){
						//this.dept = item.sort
						//this.deptName = item.name
					}else{
						this.itemRecord.TSize=item.name
					}
				}
				if(this.showType){
					if(this.showActType==1){
						this.type = item.sort
						this.typeName = item.name
					}else{
						this.itemRecord.TType=item.sort
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
				this.showCorp = false
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
	
	.grid {
	    display: grid;
	    grid-template-columns: repeat(1, 1fr);
	}
	
	.item {
		display: flex;
		padding: 20rpx 20rpx 20rpx 10rpx;
	}
	
	image {
		width: 120rpx;
		flex: 0 0 120rpx;
		height: 150rpx;
		margin-right: 30rpx;
		border-radius: 12rpx;
	}
	
	.title-wrap {
		margin-left: 30rpx;
		text-align: left;
		font-size: 28rpx;
		color: $u-content-color;
		margin-top: 0rpx;
	}
	
	.title {
		text-align: left;
		font-size: 28rpx;
		color: $u-content-color;
		margin-top: 0rpx;
	}
	
	.footerClass {
		width: 93.5%;
		display: flex;
		top: 100rpx;
		left: 155rpx;
		height: 56rpx;
	
		.price {
			font-size: 26rpx;
			color: #ff1030;
			width: 80rpx;
			height: 56rpx;
			line-height: 56rpx;
			padding-right: 10rpx;
		}
	
		.mPrice {
			font-size: 24rpx;
			color: #a2a2a2;
			width: 80rpx;
			height: 56rpx;
			line-height: 56rpx;
			text-decoration: line-through;
		}
	
		.footerBtn {
			// padding-left:170rpx;
			padding-left: 96rpx;
			display: flex;
			margin-top: 40rpx;
	
			.btn {
				width: 50rpx;
				height: 50rpx;
			}
	
			.inputClass {
				width: 50rpx;
				height: 50rpx;
				padding: 0rpx !important;
			}
		}
	
	}
	
</style>
