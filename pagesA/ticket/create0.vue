<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">建立工单</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
			
		</view>
		<view class="body u-page__item">
			<u-action-sheet :show="false" :actions="ticketLists" title="请选择工序工价表" description="..."
				@close="showTicket = false" @select="selectPicker">
			</u-action-sheet>
			<view class="u-bg-gray from-item">
				<u-form-item :labelWidth="labelWidth" label="工序工价" borderBottom
					@click="showTicket=true; hideKeyboard()">
					<u--input v-model="selectedTicket.name" disabled disabledColor="#ffffff" placeholder="请选择工序工价表"
						border="none"></u--input>
					<u-icon slot="right" name="arrow-right"></u-icon>
				</u-form-item>
			</view>
				
			<view class="page-box" v-if="showPage==0">
				<view style="margin:5px;">
					<u--form>
						<u-form-item label="名称" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TName" border="none" placeholder="输入名称" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[21].TChecked" :label="TitleInfo[21].TName" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TSaleNo" border="none" :placeholder="TitleInfo[21].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[4].TChecked" :label="TitleInfo[4].TName" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TType" border="none" :placeholder="TitleInfo[4].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[3].TChecked" :label="TitleInfo[3].TName" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TStyle" border="none" :placeholder="TitleInfo[3].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[7].TChecked" :label="TitleInfo[7].TName" :label-width="labelWidth" borderBottom>
							<u--input type="digit" v-model="itemRecord.TQuantity" border="none" :placeholder="TitleInfo[7].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[5].TChecked" :label="TitleInfo[5].TName" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TColor" border="none" :placeholder="TitleInfo[5].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TitleInfo[6].TChecked" :label="TitleInfo[6].TName" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TSize" border="none" :placeholder="TitleInfo[6].TName" :customStyle="inputStyle"></u--input>
						</u-form-item>
						<u-form-item v-if="TIsTop" label="置顶:" :label-width="labelWidth" borderBottom>
							<u-checkbox-group v-model="checkTIsTop" placement="column" @change="checkboxChange" style="padding-top: 5px;padding-left: 10px;">
								<u-checkbox size="24" labelSize="24" :customStyle="{marginBottom: '10px'}" label="_" name="itemTIsTop">
								</u-checkbox>
							</u-checkbox-group>	
						</u-form-item>
						
						<u-form-item label="工单张数:" :label-width="labelWidth" borderBottom>
							<u--input type="number" v-model="quantity" border="none" placeholder="工单张数" :customStyle="inputStyle"></u--input>
						</u-form-item>
					</u--form>
				</view>
				<view v-if="canEdit">
					<view class="custom-btn" style="text-align: center;">
						{{msg}}
					</view>
					<view class="custom-btn">
						<u-button type="primary"  @click="save(1)">储存</u-button>
					</view>
					<view class="custom-btn">
						<u-button type="primary"  @click="doScan()">扫码录入</u-button>
					</view>
				</view>
				
			</view>
			
			<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelDelete" @confirm="save(2)" ref="uModal"
				:async-close="false" cancel-text="取消" confirm-text="确认删除">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
						<text class="font-red">操作删除后记录无法恢复. </text>
				</view>
			</u-modal>
			
			<u-picker
				:show="showTicket"
				:columns="ticketLists"
				keyName="TName"
				ref="uPicker1"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>

			<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelFilter" @confirm="getTicket()" ref="uModal"
				:async-close="false" cancel-text="重设" confirm-text="搜索">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="80" class="u-form-item-area" label="关键词">
							<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
						</u-form-item>
					</u-form>
				</view>
			</u-modal>
			<u-action-sheet :show="showTSn" :actions="snLists" title="请选择裁床号" description="..."
				@close="showTSn = false" @select="snSelect">
			</u-action-sheet>
			<u-action-sheet :show="showStyle" :actions="styleLists" title="请选择款号" description="..."
				@close="showStyle = false" @select="styleSelect">
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
		getTicket,
		saveBarcode,
		saveBarcodeLists,
		astCf,
		astMp
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				checkTIsTop:[],
				labelWidth: 100,
				msg:'',
				quantity: 1,
				canEdit: false,
				current: 0,
				showPage: 0,
				itemIdx: 0,
				itemRecord: {TGuid:'',TNo:'',TName:'',TSaleNo:'',TProductGuid:0,TQuantity:1,TQuantityB:1,TSize:'',TColor:''},
				keyword: '',
				//minDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -730))),
				//maxDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), 365))),
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showTicket: false,
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showTSn: false,
				showStyle: false,
				sn: '',
				style: '',
				snLists: [{
						name: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],
				selectedTicket:{},
				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				ticketLists: [],
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputStyle:'backgroundColor:#fff;margin:4rpx;'
				inputStyle:'',
				customBtn:{
					color:'red',
					margin:'30rpx'
				},
				TitleInfo:[],
				TIsTop:false

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
			for(var i=0;i<25;i++){
				this.TitleInfo.push({Id:i,TName:'',TChecked:false});
			}
			this.postAstMp('getParameter',100);
			this.canEdit=this.adminWrite[8];
			this.getTicket()
			//this.loadmore();
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
			checkboxChange(val) {
				this.checkTIsTop = val;
				console.log(this.checkTIsTop.length)
			},
			async postAstMp(action,type) {
				let params = {
					action,
					companyNo: this.userInfo.company_no,//this.globalInfo.TUserId1,
					page: 1,
					pageSize: 20,
					type: type
				}
				let res = await astMp(params);
				//uni.hideLoading();
				if (res!=undefined){
					if(res.dataLists==undefined)
						return;
					if(type==100){
						this.TitleInfo=res.dataLists;
						console.log(this.TitleInfo);
						if(this.TitleInfo[10].TName=='置顶'){
							this.TIsTop=true;
						}
					}
				}
				
			},
			async getTicket() {
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					keyword:this.keyword,
					resType: 1,
					pageSize: 200
				}
				this.ticketLists=[];
				uni.showLoading({
					'title': '正在加载数据...'
				});
				let res = await getTicket({
					params
				});
				this.showFilter=false
				//let data = res.dataLists
				this.ticketLists.push(res.dataLists);
				if(res.dataLists.length>0){
					this.selectedTicket=res.dataLists[0]
					this.itemRecord.TName=this.selectedTicket.TName
					this.itemRecord.TStyle=this.selectedTicket.TName
					this.itemRecord.TRemark=this.selectedTicket.TRemark
				}
			
				uni.hideLoading();
			},
			doScan() {
				let that = this
				uni.scanCode({
					success: function(res) {
						that.itemRecord.TStyle=res.result;
						that.save(0);
					}
				});
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
					companyNo: this.userInfo.company_no,
					page: this.page,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					//keyword:this.keyword,
					sn: this.sn,
					style: this.style
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getBarcode({
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
					this.snLists=this.queryInfo.snLists
					this.styleLists=this.queryInfo.styleLists
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
			},
			async save(actType){
				this.msg='';
				let dataLists=[]
				this.itemRecord.TIsTop=false;
				if(this.checkTIsTop.length>0)
					this.itemRecord.TIsTop=true;
				for(let i=0;i<this.quantity;i++){
					dataLists.push(this.itemRecord)
				}
				let barcodeType=0;
				if(this.globalInfo.MyQrcode)
					barcodeType=1;
				this.itemRecord.TQuantityB=this.itemRecord.TQuantity * 1.0
				let params = {
					actType: actType,
					companyNo: this.userInfo.company_no,
					uid: this.globalInfo.DefaultUser.uid,
					itemRecord: this.itemRecord,
					dataLists: dataLists,
					delLists: [],
					TProductGuid: this.selectedTicket.TProductGuid,
					TTicketGuid: this.selectedTicket.TGuid,
					TSaleNo: this.itemRecord.TSaleNo,
					TStyle: this.selectedTicket.TStyle,
					TStyleName: this.selectedTicket.TName,
					barcodeType:barcodeType,
					TQuantity: this.itemRecord.TQuantity * 1.0,
					TQuantityB: this.itemRecord.TQuantity * 1.0,
					TNoStrart: '',
					TSn: '',
					TContent: '',
					TCreateType: 0
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				//post wu {}
				let res = await saveBarcodeLists(params);
				uni.hideLoading();
				
				this.showBarcode = false
				this.dataLists = []
				this.selectedColorLists = []
				this.msg=res.msg;
				this.checkTIsTop=[];
				uni.navigateBack()
				// uni.navigateTo({
				// 	url: '/pages/ticket/barcode'
				// })
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
			},
			changePicker(e) {
				console.log('change', e);
			},			
			selectPicker(e) {
				this.selectedTicket = e.value[0];
				this.selectedTicket.name=this.selectedTicket.TNo+' - '+this.selectedTicket.TName
				this.itemRecord.TName=this.selectedTicket.TName
				this.itemRecord.TStyle=this.selectedTicket.TName
				this.itemRecord.TRemark=this.selectedTicket.TRemark
				this.itemRecord.TProductGuid=this.selectedTicket.TProductGuid
				this.showTicket = false;
				console.log(this.selectedTicket)
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.showTicket = false
			},
			cancelPicker() {
				// console.log('cancel');
				this.showTicket = false
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
