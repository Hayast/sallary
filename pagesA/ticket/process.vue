<template>
	<view class="container">
		<view class="header">
			<u-row customStyle="margin-bottom: 10px">
				<u-col span="11">
					<view class="u-demo-block__content">
						<u-tabs :list="tabLists" :current="rptType" @change="changeTabsVal"></u-tabs>
					</view>
				</u-col>
				<u-col span="1">
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</u-col>
			</u-row>
		</view>
		<view class="body">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="12">
								<view>
									<span class="text-no">{{index+1}}.</span> <span v-if="rptType<4">{{item.TNo || ''}}</span>
									<span v-if="rptType==4">{{item.TGuid || ''}}</span>-{{item.TName || ''}} {{item.TCorpName || ''}}{{item.TMo || ''}}
								</view>
							</u-col>
						</u-row>
						<u-row v-if="rptType<4">
							<u-col span="12">
								<u-line-progress :percentage="item.TFinishPercent" activeColor="#0eab42"></u-line-progress>
							</u-col>
						</u-row>
						<u-row v-if="rptType==4">
							<u-col span="12" @click="showSaleInfo(item)">
								<u-line-progress v-if="item.TFinishPercent>0" :percentage="item.TFinishPercent" activeColor="#0eab42"></u-line-progress>
							</u-col>
						</u-row>
						<u-row v-for="(sku, skuIdx) in item.TSkuLists" :key="skuIdx" class="item-row" :class="skuIdx%2==0?'bg-1':'bg-2'">
							<u-col span="5">
								<view style="font-size:0.8rem;">
									{{skuIdx+1}}).{{sku.TName}}<span style="color:#2B85E4;" v-if="sku.TNum1>1">X{{FormatNum(sku.TNum1)}}</span>
								</view>
							</u-col>
							<u-col span="1"><span style="color:red;font-size: 0.7rem;">{{sku.TExist}}</span></u-col>
							<u-col span="2"><span style="color:green;font-size: 0.7rem;">{{sku.TFinish}}</span></u-col>
							<u-col span="2">
								<u-line-progress v-if="sku.TFinishPercent>0" :percentage="sku.TFinishPercent" activeColor="#55aaff"></u-line-progress>
							</u-col>
							<u-col span="2">
								<view v-for="(ord, ordIdx) in sku.TOrderLists" :key="ordIdx" style="font-size: 0.7rem;">
									<span style="color:green;" v-if="!showMi"  @click="showMi=true">{{ord._id}}</span>
									<span v-if="showMi" @click="showMi=false">
										{{ord.TDt.substring(5,11)}} ({{ord.quantity}} ) 
									</span>
								</view>
							</u-col>
							
						</u-row>
						<u-row >
							<u-col span="2">
								<span style="margin-left:10rpx;color:#2B85E4;font-size: 0.8rem;">
									{{item.TColor || ''}}
								</span>								
							</u-col>
							<u-col v-if="rptType<4" span="3">
								<span style="color:#2B85E4;font-size: 0.8rem;">
									{{item.TSize || ''}}
								</span>
							</u-col>
							<u-col span="3">
								<span style="color:#015a13;font-size: 0.8rem;text-decoration: underline;">
									{{item.TQuantity}}
								</span>
							</u-col>
							<u-col span="4">
								<span style="color:#2B85E4;font-size: 0.8rem;">
									{{ FormatDt(item.TDt) }}
								</span>
							</u-col>
							<u-col v-if="rptType==4" span="3" @click="showSaleInfo(item)">
								<span style="color:blue;font-size: 0.8rem;">进度详情</span>
							</u-col>
						</u-row>

					</view>
					<u-loadmore :status="loadStatus" @loadmore="loadmore" bgColor="#f2f2f2"></u-loadmore>
				</view>
				<view class="page-box" v-if="showPage==1">
					<view>
						<u--form>
							<u-form-item label="订单编号:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TGuid||''}}
							</u-form-item>
							<u-form-item label="客户:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TCorpName||''}}
							</u-form-item>
							<u-form-item label="客户单号:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TMo||''}}
							</u-form-item>
							<u-form-item label="数量:" :label-width="labelWidth" borderBottom>
								{{itemRecord.TQuantity||''}}
							</u-form-item>							
						</u--form>
					</view>
					<view v-for="(products, idx2) in itemRecord.TDetailLists" :key="idx2" class="item-row" :class="idx2%2==0?'bg-1':'bg-2'">
						<view v-for="(colors, idx3) in products.TDetailLists" :key="idx3" class="item-row" :class="idx3%2==0?'bg-1':'bg-2'">
							<view v-for="(sizes, idx4) in colors.SizeList" :key="idx4" class="item-row" :class="idx4%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="12">
										<view>
											<span style="color: blue;">{{products.TName}} {{colors.TName}}  {{sizes.TName}}</span> ({{sizes.TQty}})
										</view>
									</u-col>
								</u-row>
								<u-row>
									<u-col span="12">
										<u-line-progress :percentage="sizes.TFinishPercent" activeColor="#0eab42"></u-line-progress>
									</u-col>
								</u-row>
								<u-row v-for="(sku, idx5) in sizes.TSkuLists" :key="idx5" class="item-row" :class="idx5%2==0?'bg-1':'bg-2'">
									<u-col span="5">
										<view>
											{{idx5+1}}.{{sku.TName}}<span style="color:#2B85E4;" v-if="sku.TNum1>1">X{{FormatNum(sku.TNum1)}}</span>
										</view>
									</u-col>
									<u-col span="2"><span style="color:red;" v-if="sku.TExist!=0">{{sku.TExist}}</span></u-col>
									<u-col span="2"><span style="color:green;" v-if="sku.TFinish!=0">{{sku.TFinish}}</span></u-col>
									<u-col span="3">
										<u-line-progress :percentage="sku.TFinishPercent" activeColor="#55aaff"></u-line-progress>
									</u-col>
								</u-row>
								<u-row v-if="sizes.TPInQty>0">
									<u-col span="6">
										<span v-if="sizes.TIsLgQty==1" style="clolr:red;">
											完成数量:{{sizes.TPInQty}}
										</span>
									</u-col>
									<u-col span="3">
										入库数量:
									</u-col>
									<u-col span="3">
										<span style="color:blue;">
											{{sizes.TPInQty}}
										</span>
									</u-col>
								</u-row>
								<u-row v-if="sizes.TPInQty==0 && sizes.TFinishPercent>0">
									<u-col span="4">
									</u-col>
									<u-col span="5">
										<u--form>
											<u-form-item label="入库:" :label-width="60" borderBottom>
												<u--input type="number" v-model="sizes.TInQty" border="none" placeholder="入库数量" :customStyle="inputStyle"></u--input>
											</u-form-item>	
										</u--form>
									</u-col>
									<u-col span="2">
										<u-button type="primary" size="mini" @click="doPin(idx2,idx3,idx4)">确定</u-button>
									</u-col>
								</u-row>
							</view>
						</view>
						<u-divider text="总体进度"></u-divider>
						<u-row>
							<u-col span="12">
								<u-line-progress :percentage="products.TFinishPercent" activeColor="#0eab42"></u-line-progress>
							</u-col>
						</u-row>
						<u-row v-for="(sku, idx5) in products.TSkuLists" :key="idx5" class="item-row" :class="idx5%2==0?'bg-1':'bg-2'">
							<u-col span="5">
								<view>
									{{idx5+1}}.{{sku.TName}}<span style="color:#2B85E4;" v-if="sku.TNum1>1">X{{FormatNum(sku.TNum1)}}</span>
								</view>
							</u-col>
							<u-col span="2"><span style="color:red;" v-if="sku.TExist!=0">{{sku.TExist}}</span></u-col>
							<u-col span="2"><span style="color:green;" v-if="sku.TFinish!=0">{{sku.TFinish}}</span></u-col>
							<u-col span="3">
								<u-line-progress :percentage="sku.TFinishPercent" activeColor="#55aaff"></u-line-progress>
							</u-col>
						</u-row>
						
					</view>
					<view>
						<view v-if="false" class="custom-btn">
							<u-button type="primary"  @click="save(1)">储存</u-button>
						</view>
						<view class="custom-btn">
							<u-button type="primary" :class="custom-btn" @click="showPage=0">返回</u-button>
						</view>
					</view>
					
				</view>
			</scroll-view>
			
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
						<u-form-item :labelWidth="labelWidth" label="订单号" borderBottom
							@click="showSaleNo=true; hideKeyboard()">
							<u--input v-model="saleNo" disabled disabledColor="#ffffff" placeholder="请选择订单号"
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
				:show="showStyle"
				:columns="styleLists"
				keyName="_id"
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
		savePin,
		getProgress
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				showMi:false,
				labelWidth: 100,
				rptType: 2,
				current: 0,
				showPage: 0,
				changedDt: false,
				canEdit: false,
				canApply: false,
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
				showSn: false,
				showStyle: false,
				showSaleNo: false,
				saleNoLists:[{_id:''}],
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

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputStyle:'backgroundColor:#fff;margin:4rpx;'
				inputStyle:'',
				customBtn:{
					color:'red',
					margin:'30rpx'
				},
				tabLists: [{
						name: '款号'
					},
					{
						name: '裁床号'
					},
					{
						name: '单号'
					},
					{
						name: '名称'
					},
					{
						name: '订单'
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
			//this.changeTabsVal({index:2,name:this.tabLists[2].name});
			var pages = getCurrentPages() //获取加载的页面
			var currentPage = pages[pages.length - 1] //获取当前页面的对象
			var url = "/" + currentPage.route //当前页面url
			for (var i = 0; i < this.urlLists.length; i++) {
				if (this.urlLists[i].url == url) {
					this.tabValue = i;
					break;
				}
			}
			this.canEdit=this.adminWrite[9];
			this.canApply=this.adminApply[9];
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
			showSaleInfo(item){
				this.itemRecord=item;
				this.showPage=1;
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
			edit(item,itemIdx){
				this.itemIdx=itemIdx
				this.itemRecord=item
				this.showPage=1
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let actDt=0;
				if(this.changedDt)
					actDt=1;
				this.changedDt=false;
				let params = {
					companyNo: this.userInfo.company_no,
					page: this.page,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					showDel:0,
					actDt,
					actType:0,
					rptType: this.rptType,
					keyword:this.keyword,
					sn: this.sn,
					style: this.style,
					saleNo: this.saleNo
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getProgress({
					params
				});
				let data = res.dataLists
				this.queryInfo = res.queryInfo
				for (let i = 0; i < data.length; i++) {
					this.dataLists.push(data[i])
				}
				console.log(this.dataLists)
				uni.hideLoading();
				this.loadStatus = 'loadmore';
				if(this.queryInfo.page==1){
					this.snLists=[]
					this.snLists.push(this.queryInfo.snLists)
					this.styleLists=[]
					this.styleLists.push(this.queryInfo.styleLists)
					//if(actDt==1){
						this.saleNoLists=[]
						this.saleNoLists.push(this.queryInfo.saleLists)
					//}
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			async doPin(idx2=0,idx3=0,idx4=0){
			  let saleNo		=	this.itemRecord.TGuid;
			  let productGuid	=	this.itemRecord.TDetailLists[idx2].TGuid;
			  let productName	=	this.itemRecord.TDetailLists[idx2].TName;
			  let colorName		=	this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].TName;
			  let sizeName		=	this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TName;
			  let quantity		=	this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TQty;
			  let inQty			=	this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TInQty;
			  console.log(inQty);
			  if(inQty==undefined || inQty==''){
				  uni.showToast({
				  	title:'请填入入库数量.'
				  })
				  return;
			  }
			  let orderList=[];
			  if(quantity!=inQty && quantity!=0){
				let skuList=this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TSkuLists;
				for(let i=0;i<skuList.length;i++){
				  let skuOrderList=skuList[i].TOrderLists;
				  for(let j=0;j<skuOrderList.length;j++){
					let found=false;
					let qty=0;
					let ordIdx=0;
					for(let k=0;k<orderList.length;k++){
					  if(orderList[k].TUserId==skuOrderList[j].TUserId && orderList[k].TSkuGuid==skuOrderList[j].TSkuGuid){
						found=true;
						qty=skuOrderList[k].TQuantity;
						ordIdx=k;
					  }
					}
					if(found){
					  orderList[ordIdx].TQuantity=orderList[ordIdx].TQuantity+skuOrderList[j].TQuantity;
					}else{
					  orderList.push(this.deepClone(skuOrderList[j]));
					}
				  }
				}
				let sQty=parseInt(quantity)-parseInt(inQty);
				for(let i=0;i<orderList.length;i++){
				  orderList[i].TQuantity= - sQty * parseFloat(orderList[i].TQuantity) / parseFloat(quantity);
				  orderList[i].TAmt= parseFloat(orderList[i].TQuantity) * parseFloat( orderList[i].TPrice);
				  orderList[i].TRemark='入库扣除';
				}
				//console.log('orderList',orderList);
			  }
			  let params = {
				companyNo: this.userInfo.company_no,
				actType: 0,
				pioInfo:{saleNo,productGuid,productName,colorName,sizeName,quantity,inQty,orderList}
			  };
			  console.log(params);
			  this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TPInQty=this.itemRecord.TDetailLists[idx2].TDetailLists[idx3].SizeList[idx4].TInQty;
			  let res = await savePin(params);
			  console.log(res);
			},
			changeTabsVal(item) {
				this.rptType = item.index
				this.showPage =0 
				this.doSearch()
			},
			cancel(){
				this.cancelFilter();
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
				this.saleNo=''
			},
			
			navigateTo(url) {
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				uni.switchTab({
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
			saleNoSelect(e) {
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
				if(this.showSaleNo)
					this.saleNo=item._id
				//console.log(this.saleNo)
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
