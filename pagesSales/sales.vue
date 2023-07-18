<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">销售订单管理</view>
				<view v-if="showPage==0" style="width:160upx;">
						<view style="width:80upx;float: left;">
							<u-icon name="search" color="#0989de" size="36" @click="showFilter=true"></u-icon>
						</view>
						<view style="width:80upx;">
							<u-icon v-if="canEdit" name="plus-circle-fill" color="#2775f3" size="36" @click="add()"></u-icon>
						</view>
				</view>
				<view v-if="showPage==1 || showPage==3">
					<u-icon name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
				<view v-if="showPage==2">
					<u-icon name="play-left-fill" color="#2979ff" size="24" @click="savePItem()"></u-icon>
				</view>
			</view>
		</view>
		<view class="body u-page__item">
			<scroll-view v-if="showPage==0" scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row"
						:class="index%2==0?'bg-1':'bg-2'">
						<u-row @click="showPage=3;itemRecord=item">
							<u-col span="9">
								<view v-if="canEdit" @click="edit(1,item,index)">
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TCorpName}} {{item.TPhone || ''}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}.</span>{{item.TGuid}} {{item.TCorpName}} {{item.TPhone || ''}}
								</view>
							</u-col>
							<u-col span="3">
								{{statusLists[0][item.TStatus]}}
							</u-col>
						</u-row>
						<u-row>
							<u-col span="4">
								<span style="margin-left:10rpx;">
									<view>
										{{item.TMoNo || ''}}{{item.TMo || ''}}
									</view>
								</span>
							</u-col>
							<u-col span="2">
									{{item.TQuantity || ''}}
							</u-col>
							<u-col span="1">
									{{item.TAmt || ''}}
							</u-col>
							<u-col span="2">
								{{item.TDt.substring(5,11) || ''}}
							</u-col>
							<u-col :span="1" @click="edit(1,item,index)">
								<u-icon name="edit-pen-fill" color="#0055ff" size="24"></u-icon>
							</u-col>
							<u-col v-if="canEdit" :span="1" @click="edit(2,item,index)">
								<u-icon v-if="item.TStatus<1 && (canEdit || canApply)" name="trash-fill" color="#ffaa00" size="24"></u-icon>
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
							<view>
								{{itemRecord.TGuid}}
							</view>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="来往单位" borderBottom>
							<view @tap="showFilterCorp=true">
								{{itemRecord.TCorpName || '选择来往单位'}}
							</view>
							</u--input>
							<u-icon slot="right" name="arrow-right" @click="showCorp=true;showActType=1; hideKeyboard()"></u-icon>
						</u-form-item>
						<u-form-item label="联系电话:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TPhone" border="none" placeholder="输入联系电话"
								:customType="inputType"></u--input>
						</u-form-item>
						<u-form-item label="制单号:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TMoNo" border="none" placeholder="输入制单号" type="text"
								:customType="inputType"></u--input>
						</u-form-item>
						<u-form-item label="客户单号:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.TMo" border="none" placeholder="客户单号" type="text"
								:customType="inputType"></u--input>
						</u-form-item>
						<u-form-item :labelWidth="labelWidth" label="订单状态" borderBottom
							@click="showStatus=true; hideKeyboard()">
							{{statusLists[0][itemRecord.TStatus]}}
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
						<u-form-item v-if="canEdit" :labelWidth="labelWidth" label="产品列表" borderBottom>
							<view @tap="showFilterItem=true;">选择增加产品</view>
							<u-icon slot="right" name="plus-circle-fill" @click="addItem(0)"></u-icon>
						</u-form-item>
						<view v-for="(item, index) in itemRecord.TDetailLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
							<u-row>
								<u-col span="3">
									<span class="text-no" style="float: left;">{{index+1}}.</span>
									<u--input v-model="item.TGuid" border="none" placeholder="条码"></u--input>
								</u-col>
								<u-col span="5">
									{{item.TName||''}} {{item.TColor||''}}{{item.TSize||''}}{{item.TUnit||''}} {{item.TPrice||''}}
								</u-col>
								<u-col span="2">
									<u--input type="digit" input-align="center" v-model="item.TQuantity" border="none" placeholder="数量"></u--input>
								</u-col>
								<u-col span="1">
									<u-icon style="float: left;" name="order" color="#009fef" size="24" @click="editPItem(index)"></u-icon>
								</u-col>
								<u-col span="1">
									<u-icon v-if="canEdit" name="trash-fill" color="#ff5500" size="24" @click="deleteItem(index)"></u-icon>
								</u-col>
							</u-row>
						</view>
					</u--form>
				</view>
				<view>
					<view class="custom-btn">
						<u-button v-if="item.TStatus<1 || (canEdit || canApply)" type="primary"  @click="save(actType)">储存</u-button>
					</view>
					<view v-if="itemRecord._id!='' && actType==2 && (canEdit || canApply)" class="custom-btn">
						<u-button type="warning" :class="custom-btn" @click="save(4)">删除</u-button>
					</view>
				</view>			
			</view>
			<view class="page-box" v-if="showPage==2">
				<view style="width:100%;float: left;">
					<view>
						<u-row customStyle="margin-bottom: 1px">
							<u-col span="11">
								<view class="u-demo-block__content">
									<u-tabs :list="colorNameLists" @change="changeColor"></u-tabs>
								</view>
							</u-col>
							<u-col span="1">
								<view v-if="canEdit">
									<u-icon name="reload" color="#2979ff" size="24" @click="resetColorSize(0)"></u-icon>
								</view>
							</u-col>
						</u-row>
						<u-row customStyle="margin-bottom: 1px">
							<u-col span="12">
								<view class="u-demo-block__content">
									<u-tabs :list="sizeNameLists" @change="changeSize"></u-tabs>
								</view>
							</u-col>
						</u-row>
					</view>
				</view>
				<view class="body">
					<scroll-view scroll-y style="height: 100%;width: 100%;background-color: white;">
							<view v-for="(item, index) in selectedColorLists" :key="index">
								<view class="flex">
									<view>
										<u-row>
											<u-col :span="10">
												<view style="margin-right:30rpx;">
													{{item.TName}}
												</view>
											</u-col>
											<u-col :span="2">
												<u-icon color="#ffaa00" size="24" name="trash-fill"
													@click="delColorIdx=index;showDel=true;"></u-icon>
											</u-col>
										</u-row>
									</view>
									<view v-for="(size,sIdx) in item.SizeList" :key="sIdx"
										style="padding-left: 30px;padding-right: 80px;background-color: ghostwhite;">
										<u-form-item :label="size.TName" borderBottom>
											<u--input type="number" v-model="size.TQty" border="none" :placeholder="phSizeQty"></u--input>
										</u-form-item>
									</view>
								</view>
							</view>
					</scroll-view>
				</view>
				<view>
					<view class="custom-btn">
						<u-button type="primary"  @click="savePItem()">储存</u-button>
					</view>
				</view>
			</view>	
			<view class="page-box" v-if="showPage==3">
				<view style="font-size:1.1em;">
					<view>
						<span class="labelTitle">编号:</span>{{itemRecord.TGuid||''}}
					</view>
					<view>
						<span class="labelTitle">来往单位:</span>{{itemRecord.TCorpName||''}}
					</view>
					<view>
						<span class="labelTitle">联系电话:</span>{{itemRecord.TPhone||''}}
					</view>
					<view>
						<span class="labelTitle">制单号:</span>{{itemRecord.TMoNo||''}}
					</view>
					<view>
						<span class="labelTitle">客户单号:</span>{{itemRecord.TMo||''}}
					</view>
					<view>
						<span class="labelTitle">订单状态:</span>{{statusLists[0][itemRecord.TStatus]}}
					</view>
					<view>
						<span class="labelTitle">客户单号:</span>{{itemRecord.TMo||''}}
					</view>
						
					<view v-for="(item, indexC1) in itemRecord.TDetailLists" :key="indexC1" class="item-row" :class="indexC1%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="3">
								<span class="text-no" style="float: left;">{{indexC1+1}}.</span>
								{{item.TGuid}}
							</u-col>
							<u-col span="5">
								{{item.TName||''}} {{item.TColor||''}}{{item.TSize||''}}{{item.TUnit||''}} {{item.TPrice||''}}
							</u-col>
							<u-col span="2">
								{{item.TQuantity}}
							</u-col>								
						</u-row>
						<u-row>
							<u-col>
								<view v-for="(item2, indexC2) in item.TDetailLists" :key="indexC2">
									<view class="flex">
										<view>
											<u-row>
												<u-col :span="10">
													<view style="margin-right:30rpx;">
														{{item2.TName}}
													</view>
												</u-col>
											</u-row>
										</view>
										<view v-for="(size,sIdx) in item2.SizeList" :key="sIdx"
											style="padding-left: 30px;padding-right: 80px;background-color: ghostwhite;">
											<u-row>
												<u-col span="6">{{size.TName||''}}</u-col>
												<u-col span="4">{{size.TQty||''}}</u-col>
											</u-row>
											 
										</view>
									</view>
								</view>
							</u-col>
							
						</u-row>
					</view>
				</view>
				<view>
					<view class="custom-btn">
						<u-button type="primary"  @click="showPage=0">返回</u-button>
					</view>
				</view>
			</view>
			
		</view>
		<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelFilter" @confirm="doSearch()" ref="uModal"
			:async-close="false" cancel-text="重设" confirm-text="搜索">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
						<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
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
			:show="showStatus"
			:columns="statusLists"
			keyName="name"
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
		
		<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelDelete" @confirm="save(2)" ref="uModal" :async-close="false"
			cancel-text="取消" confirm-text="确认删除">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<text class="font-red">操作删除后记录无法恢复. </text>
			</view>
		</u-modal>
		
		
		<u-modal :show="showFilterCorp" title="选择公司" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="cancelFilter" ref="uModal" :async-close="false" cancel-text="关闭">
			<view>
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
						<u-input v-model="keyword_corp" @change="doSearchCorp()" />
					</u-form-item>						
				</u-form>
				<view style="height: 400upx;">
					<scroll-view scroll-y style="height: 100%;width: 100%;">
						<view class="page-box">
							<view v-for="(item, index) in corpLists" :key="index" class="item-row-5"
								:class="index%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="4"><span class="text-no">{{index+1}}.</span> {{item.username}}</u-col>
									<u-col span="6">
										{{item.nickname}}
									</u-col>
									<u-col span="2" @click="selectCorp(item)">
										<u-icon name="checkmark" color="#2979ff" size="24"></u-icon>
									</u-col>
								</u-row>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</u-modal>
		<u-modal :show="showFilterItem" title="选择产品" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="cancelFilter" ref="uModal" :async-close="false" cancel-text="关闭">
			<view>
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
						<u-input v-model="keyword_item" @change="doSearchItem()" />
					</u-form-item>						
				</u-form>
				<view style="height: 400upx;">
					<scroll-view scroll-y style="height: 100%;width: 100%;">
						<view class="page-box">
							<view v-for="(item, index) in itemLists" :key="index" class="item-row3"
								:class="index%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="5"><span class="text-no">{{index+1}}.</span> {{item.TGuid||''}}</u-col>
									<u-col span="5">
										 {{item.TName||''}}
										{{item.TColor||''}}
										{{item.TSize||''}}
										{{item.TPrice||''}}
									</u-col>
									<u-col span="1" @click="addItemData(item)">
										<u-icon v-if="canEdit" name="plus" color="#2979ff" size="24"></u-icon>
									</u-col>
								</u-row>
							</view>
						</view>
					</scroll-view>
				</view>
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
		saveSaleOrder,
		getSaleOrder,
		getProductItem,
		getProduct,
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
				isShow: true,
				colorLists: [],
				idxPd:0,
				showMoType:false,
				moTypeLists: [],
				moType: '',
				showStatus:false,
				statusLists:[['建立订单','确认订单','制作生产','完成订单']],
				colorNameLists: [],
				selectedColorLists: [],
				selectedSizeLists: [],
				sizeLists: [],
				sizeNameLists: [],
				keyword_corp:'',
				keyword_item:'',
				showFilterCorp:false,
				showFilterItem:false,
				ioType:1,
				ioTypes:['产品入库','产品出库','业务订单'],
				labelWidth: 100,
				current: 0,
				canEdit: false,
				canApply: false,
				userType:0,
				actType:0,
				showPage: 0,
				showActType: 0,
				showCorp: false,
				showProductDetail: false,
				corpLists:[],
				itemLists:[],
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
			let idxAdmin=12;
			this.canEdit=this.adminWrite[idxAdmin];
			this.canApply=this.adminApply[idxAdmin];
			this.getParameter(0)
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
			async doSearchCorp() {
				let params = {
					page: 1,
					companyNo: this.userInfo.company_no,
					action: 'getUser',
					userType:this.ioType+1,
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
					companyNo: this.userInfo.company_no,
					page: 1,
					type:-1,
					actType:0,
					//tokenYun:this.userInfo.token,
					//type: this.type,
					ioType:this.ioType,
					keyword: this.keyword_item
				}
				
				let res = await getProduct({params});
				if(res)
					this.itemLists=res.dataLists;
			},
			selectItem(item){
				this.showFilterItem=false;
			},
			selectCorp(item){
				this.itemRecord.TCorpGuid=item.guid;
				this.itemRecord.TCorpName=item.nickname;
				if(item.AddrLists.length>0){
					this.itemRecord.TPhone=item.AddrLists[0].TPhone;
					this.itemRecord.TAddress=item.AddrLists[0].TAddress;
				}		
				this.showFilterCorp=false;
			},
			editPItem(idx){
				this.idxPd=idx;
				this.selectedColorLists=this.itemRecord.TDetailLists[idx].TDetailLists;
				console.log(idx,this.selectedColorLists);
				this.showPage=2;
			},
			savePItem(){
				let idx=this.idxPd;
				this.itemRecord.TDetailLists[idx].TDetailLists=this.selectedColorLists;
				let qty=0;
				for(let i=0;i<this.selectedColorLists.length;i++){
					for(let j=0;j<this.selectedColorLists[i].SizeList.length;j++)
						qty=qty+ parseFloat(this.selectedColorLists[i].SizeList[j].TQty) ;
				}
				this.itemRecord.TDetailLists[idx].TQuantity=qty;
				this.showPage=1;
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
				this.itemRecord = {_id:'',TGuid:'',TType:0,TName:'',TMo:'',TQuantity:0,TPrice:0,TAmt:0,TStatus:0,TDetailLists:[]}
				this.showPage = 1
			},
			edit(actType,item, itemIdx) {
				this.actType=actType;
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
					listT.push({TGuid:'',TPrice:0,TQuantity:0,TDetailLists:[]})
				}else{
					for(let i=0;i<len;i++){
						k=k+1
						listT.push(this.itemRecord.TDetailLists[i])
						if(i==idx){
							k=k+1
							listT.push({TGuid:'',TPrice:0,TQuantity:0,TDetailLists:[]})
						}
					}
				}
				
				this.itemRecord.TDetailLists=listT
				this.showFilterItem=true;
				this.showFilterItem=false;
			},
			addItemData(item){
				let itemT={TProductGuid:item.TGuid,TGuid:item.TGuid,TName:item.TName,TPrice:item.TPrice,TQuantity:1,TDetailLists:[]}
				this.itemRecord.TDetailLists.push(itemT);
				this.showFilterItem=false;
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
					companyNo: this.userInfo.company_no,
					page: this.page,
					showDel:0,
					//tokenYun:this.userInfo.token,
					type: this.type,
					ioType:this.ioType,
					ioType:this.ioType,
					moType: this.moType||'',
					keyword: this.keyword,
					dt1:this.textDt1.replace(/\//g, "-"),
					dt2:this.textDt2.replace(/\//g, "-")
				}
				this.page = this.page + 1
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//post wu {}
				let res = await getSaleOrder({params});
				let data = res.dataLists
				if(!data){
					uni.hideLoading();
					uni.showToast({
						title:'出错了...'
					})
					return;
				}
				this.queryInfo = res.queryInfo
				this.moTypeLists=[]
				this.moTypeLists.push(this.queryInfo.moTypeLists)
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
				this.showProductDetail = false
			},
			async save(actType) {
				if (actType == 4) {
					this.showDel = true
					return
				} else {
					this.showDel = false
				}
				let qty=0;
				for(let i=0;i<this.itemRecord.TDetailLists.length;i++){
					qty=qty+parseFloat(this.itemRecord.TDetailLists[i].TQuantity);
				}
				this.itemRecord.TQuantity=qty;
				
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
				console.log(params);
				let res = await saveSaleOrder(params);
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
					companyNo: this.userInfo.company_no,
					page: 1,
					resType: 1,
					pageSize: 200,
					type: type,
					ordBy: 'TSort'
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//注意get的参数  需要({params})
				let res = await getParameter({params});
				//let data = res.dataLists
			
				uni.hideLoading();
				for (let i = 0; i < res.dataLists[2].length; i++) {
					let item = res.dataLists[2][i]
					this.colorLists.push(item)
					this.colorNameLists.push({
						name: item.TName
					})
				}
				for (let i = 0; i < res.dataLists[3].length; i++) {
					let item = res.dataLists[3][i]
					this.sizeLists.push(item)
					this.sizeNameLists.push({
						name: item.TName
					})
				}
				this.setSelectedSizeList();
			},
			resetColorSize(index){
				if (index == 0) {
					this.selectedColorLists = []
					this.setSelectedSizeList()
				} else{
					this.selectedSizeLists = []
				}
			},
			changeColor(item) {
				let index=item.index;
				this.currentColor = index;
				this.selectedColorLists.push({
					TQty: 1,
					TName: this.colorNameLists[index].name,
					SizeList: this.$u.deepClone(this.selectedSizeLists)
				})
				console.log(this.selectedColorLists, this.selectedSizeLists)
			},
			changeSize(item) {
				let index=item.index;				
				this.currentSize = index;
				this.selectedSizeLists.push({
					selected: false,
					TQty: 1,
					TName: this.sizeNameLists[index].name
				})
				for (let i = 0; i < this.selectedColorLists.length; i++)
					this.selectedColorLists[i].SizeList = this.$u.deepClone(this.selectedSizeLists);
			},
			setSelectedSizeList() {
				this.selectedSizeLists = []
				// let sizeNameListsT = this.sizeNameLists; //JSON.stringify(this.sizeNameLists)
				// for (let i = 1; i < sizeNameListsT.length; i++) {
				// 	this.selectedSizeLists.push({
				// 		selected: false,
				// 		TQty: '',
				// 		TName: sizeNameListsT[i].name
				// 	})
				// }
			
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				//
			},		
			selectPicker(e) {
				let item = e.value[0];
				//console.log(item)
				if(this.showCorp){
					this.selectCorp(item)
				}
				if(this.showStatus){
					this.itemRecord.TStatus=e.indexs[0];
					this.showStatus=false;
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
				if(this.showMoType){
					this.moType=item.name
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
				this.showProductDetail = false
				this.showStatus = false
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
	.item-row {
		padding: 4rpx;
	}
	
	.form-item {
		padding-left: 5px;
		padding-right: 25px;
	}
	
	.page-box {
		margin: 5rpx;
		padding: 5rpx;
	}
	
	.wrap {
		display: flex;
		flex-direction: column;
		height: calc(100vh - var(--window-top));
		width: 100%;
	}
	
	.swiper-box {
		flex: 1;
	}
	
	.swiper-item {
		height: 100%;
	}
	
	/*scroll-view外层*/
	.s-s-p-c-wrapper {
		position: relative;
		height: 35px;
		white-space: nowrap;
		padding: 2px 10px;
	}
	
	/*左右渐变遮罩*/
	.h-c-box {
		position: absolute;
		top: 0;
		height: 100%;
		width: 32px;
		z-index: 2;
	}
	
	.h-c-box-left {
		left: 0;
		background-image: linear-gradient(to left, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}
	
	.h-c-box-right {
		right: 0;
		background-image: linear-gradient(to right, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}
	
	/*scroll-view本身*/
	.s-s-p-c {
		min-width: 100%;
	}
	
	/*scroll-view内层*/
	.s-s--s-w {
		margin-left: 2px;
		display: inline-block;
	}
	
	/*设置空白以保持美观*/
	.s-s-p-c .s-s--s-w:first-child {
		margin-left: 18px;
	}
	
	.s-s-p-c .s-s--s-w:last-child {
		margin-right: 16px;
	}
	.labelTitle{
		min-width: 110px;
		font-size: 1.2em;
		color:darkblue;
	}
</style>
