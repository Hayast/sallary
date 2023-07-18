<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view class="title-text">工序工价</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
			
		</view>
		<view class="body u-page__item">
			<scroll-view scroll-y style="height: 100%;width: 100%;" @scrolltolower="reachBottom">
				<view class="page-box" v-if="showPage==0">
					<view v-for="(item, index) in dataLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
						<u-row>
							<u-col span="12">
								<view v-if="canEdit" @click="edit(item)">
									<span class="text-no">{{index+1}}.</span> {{item.TNo}}-{{item.TName}}
								</view>
								<view v-else>
									<span class="text-no">{{index+1}}.</span> {{item.TNo}}-{{item.TName}}
								</view>
							</u-col>
						</u-row>
						<u-row>
							<u-col span="6">
								<span style="margin-left:10rpx;">
									{{item.TProductName || ''}}
									{{item.TRemark || ''}}
								</span>
							</u-col>
							<u-col span="4" @click="edit(item)">
								<span v-if="item.TDt">
									{{item.TDt.substring(0,10)}}
								</span>
							</u-col>
							<u-col span="1">
								<u-icon v-if="canEdit" name="file-text-fill" color="#2979ff" size="24" @click="copy(item)">
								</u-icon>
							</u-col>
							<u-col span="1">
								<u-icon v-if="canEdit" name="edit-pen-fill" color="#2979ff" size="24" @click="edit(item)">
								</u-icon>
							</u-col>
						</u-row>

					</view>
					<view>
						<view class="custom-btn">
							<u-button v-if="canEdit" type="primary"  @click="addNew()">新增</u-button>
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
							<u-form-item label="名称:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TName" border="none" placeholder="输入名称" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<u-form-item :labelWidth="labelWidth" label="产品" borderBottom>
								<view @tap="showFilterProduct=true">
									{{itemRecord.TGuid || ''}}{{itemRecord.TProductName || '选择产品'}}
								</view>
								</u--input>
								<u-icon slot="right" name="arrow-right" @click="showFilterProduct=true; hideKeyboard()"></u-icon>
							</u-form-item>
							<u-form-item label="备注:" :label-width="labelWidth" borderBottom>
								<u--input v-model="itemRecord.TRemark" border="none" placeholder="输入备注" :customStyle="inputStyle"></u--input>
							</u-form-item>
							<view v-for="(item, index) in itemRecord.TSkuLists" :key="index" class="item-row" :class="index%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="1"><span class="text-no">{{index+1}}.</span></u-col>
									<u-col span="7">
										<u--input v-model="item.TName" border="none" placeholder="输入名称"></u--input>
									</u-col>
									<u-col span="2">
										<u--input type="digit" v-model="item.TPrice" border="none" placeholder="输入价格"></u--input>
									</u-col>
									<u-col span="1">
										<u-icon name="plus-circle-fill" color="#2979ff" size="24" @click="addItem(index)"></u-icon>
									</u-col>
									<u-col span="1">
										<u-icon name="trash-fill" color="#ff5500" size="24" @click="deleteItem(index)"></u-icon>
									</u-col>
								</u-row>
							</view>
						</u--form>
					</view>
					<view v-if="canEdit">
						<view class="custom-btn">
							<u-button type="primary"  @click="save(actType)">储存</u-button>
						</view>
						<view class="custom-btn">
							<u-button v-if="actType!=0" type="warning" :class="custom-btn" @click="save(4)">删除</u-button>
						</view>
					</view>
					
				</view>
			</scroll-view>
		</view>
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
		<u-action-sheet :show="showTSn" :actions="snLists" title="请选择裁床号" description="..."
			@close="showTSn = false" @select="snSelect">
		</u-action-sheet>
		<u-action-sheet :show="showStyle" :actions="styleLists" title="请选择款号" description="..."
			@close="showStyle = false" @select="styleSelect">
		</u-action-sheet>
		<u-modal :show="showFilterProduct" title="选择产品" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="false" @cancel="cancelFilter" ref="uModal" :async-close="false" cancel-text="关闭">
			<view>
				<u-form ref="uForm">
					<u-form-item :labelWidth="70" class="u-form-item-area" label="关键词">
						<u-input v-model="keyword_product" @change="doSearchProduct()" />
					</u-form-item>						
				</u-form>
				<view style="height: 400upx;">
					<scroll-view scroll-y style="height: 100%;width: 100%;">
						<view class="page-box">
							<view v-for="(item, index) in productLists" :key="index" class="item-row3"
								:class="index%2==0?'bg-1':'bg-2'">
								<u-row>
									<u-col span="5"><span class="text-no">{{index+1}}.</span> {{item.TGuid||''}}</u-col>
									<u-col span="5">
										{{item.TName||''}}
										{{item.TColor||''}}
										{{item.TSize||''}}
									</u-col>
									<u-col span="1" @click="selectProduct(item)">
										<u-icon v-if="canEdit" name="checkmark" color="#2979ff" size="24"></u-icon>
									</u-col>
								</u-row>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</u-modal>
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
		getTicket,
		copyTicket,
		getProduct,
		saveTicket,
		saveTicketLists
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
				actType:0,
				canEdit: false,
				canApply: false,
				keyword_product:'',
				showFilterProduct: false,
				productLists:[],
				labelWidth: 100,
				current: 0,
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

				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				delLists: [],
				dataLists: [],
				urlLists: [],
				queryInfo: {},
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
			this.canEdit=this.adminWrite[7];
			this.canApply=this.adminApply[7];
			this.loadmore();
			this.doSearchProduct();
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
			async doSearchProduct() {
				let params = {
					page: 1,
					companyNo: this.userInfo.company_no,
					//tokenYun:this.userInfo.token,
					type: this.type,
					keyword: this.keyword_product
				}
				
				let res = await getProduct({params});
				if(res)
					this.productLists=res.dataLists;
			},
			addNew(){
				this.itemIdx=0;
				this.actType=0;
				this.itemRecord={Id:0,TGuid:'',TNo:'',TName:'',TRemark:'',TStyle:'',TSkuLists:[{TName:'',TPrice:0,TGuid:''}]}
				this.delLists = []
				this.showPage=1
			},
			selectProduct(item){
				this.itemRecord.TProductGuid=item.TGuid;
				this.itemRecord.TProductName=item.TName;
				this.showFilterProduct=false;
			},
			edit(item,itemIdx){
				this.actType=1
				this.itemIdx=itemIdx
				this.itemRecord=item
				this.delLists = []
				this.showPage=1
			},
			scrolltolower() {
				this.loadmore()
			},
			async loadmore() {
				let params = {
					companyNo: this.userInfo.company_no,
					page: this.page,
					showDel:0,
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					keyword:this.keyword,
					//sn: this.sn,
					//style: this.style
				}
				this.page = this.page + 1
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getTicket({
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
					//this.snLists=this.queryInfo.snLists
					//this.styleLists=this.queryInfo.styleLists
				}
				if(this.queryInfo.isLast){
					this.loadStatus = 'nomore'
				}
			},
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
				this.showFilterProduct=false;
			},
			async copy(item){
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					guid:item.TGuid,
					id:item._id,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:item
				}
				uni.showLoading({
					'title':'正在处理操作...'
				});
				//post wu {}
				let res = await copyTicket(params);
				uni.hideLoading();
				this.doSearch()
			},
			async save(actType){
				if(actType==4){
					this.showDel=true
					return
				}else{
					this.showDel=false
				}
				let record=this.itemRecord
				let nameLists=[];
				let pName='';
				let strSn='';
				for(let i=0;i<record.TSkuLists.length;i++){
				      if(!record.TSkuLists[i].TPriceLists)
				      record.TSkuLists[i].TPriceLists=[];
				      strSn=i.toString();
				      while(strSn.length<3)
				        strSn='0'+strSn;
				      strSn=record.TNo+strSn;
				      record.TSkuLists[i].TSn=strSn;
				      nameLists=[];
				      for(let j=record.TSkuLists[i].TPriceLists.length;j>0;j--){
				        let priceItem=record.TSkuLists[i].TPriceLists[j-1];
				        if(priceItem==undefined || priceItem.TName==''){
				          record.TSkuLists[i].TPriceLists.splice(j-1,1);
				          continue;
				        }
				        pName=priceItem.TName;
				
				        for(let k=0;k<nameLists.length;k++){
				          if(pName==nameLists[k]){
				            record.TSkuLists[i].TPriceLists.splice(j-1,1);
				            continue;
				          }
				        }
				        nameLists.push(pName);
				      }
				    }
				let params = {
					companyNo: this.userInfo.company_no,
					actType:actType,
					uid:this.globalInfo.DefaultUser.uid,
					itemRecord:record
				}
				uni.showLoading({
					'title':'正在处理操作...'
				});
				//post wu {}
				console.log(params)
				let res = await saveTicket(params);
				uni.hideLoading();
				console.log(params,res)
				if(actType==2)
					this.dataLists.splice(this.itemIdx,1)
				//this.showDel=false
				if(actType==0)
					this.doSearch();
				this.showPage=0
			},
			addItem(idx){
				let listT=[]
				let k=0;
				let len=this.itemRecord.TSkuLists.length
				for(let i=0;i<len;i++){
					k=k+1
					this.itemRecord.TSkuLists[i].TSn=k
					listT.push(this.itemRecord.TSkuLists[i])
					if(i==idx){
						k=k+1
						listT.push({TGuid:'',TName:'',TNo:k,TPrice:'',TSn:k})
					}
				}
				this.itemRecord.TSkuLists=listT
			},
			deleteItem(idx){
				this.delLists.push(this.itemRecord.TSkuLists[idx])
				this.itemRecord.TSkuLists.splice(idx,1)
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
