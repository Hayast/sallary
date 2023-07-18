<template>
	<view class="container">
		<view class="header">
			<view class="row-l-r">
				<view>{{scanTypeLists[globalInfo.ScanType].name}}</view>
				<view>
					<u-button size="small" type="primary" @click="navigateTo('/pagesA/ticket/setting')"> 扫码设定 </u-button>
				</view>
			</view>
		</view>
		
		<view class="scan-body">
			<view>
				<view v-if="resMsg.msg!='' && resMsg.sMsg=='' && resMsg.eMsg==''" style="color:orangered;">
					{{resMsg.msg||''}}
				</view>
				<view class="text-green" v-if="resMsg.sMsg!=''">
					{{resMsg.sMsg||''}} 成功.
				</view>
				<view class="text-red" v-if="resMsg.eMsg!=''">
					{{resMsg.eMsg||''}} 失败!
				</view>
				<view v-if="resMsg.msgRemark!=''">
					{{resMsg.msgRemark||''}}
				</view>
			</view>
			<view v-if="showProductInfo==1">
				<view>
					<swiper indicator-dots circular=true duration="400" style="height: 400upx;">
						<swiper-item class="swiper-item">
							<view class="image-wrapper">
								<image :src="domain+'/'+ProductInfo.TPhoto" mode="widthFix" class="swiper-img">
								</image>
							</view>
						</swiper-item>
					</swiper>
				</view>
				<view>
					<u-parse :html="ProductInfo.TRemark"></u-parse>
				</view>
			</view>
			<view v-if="globalInfo.ScanType==2">
				<u-row>
					<u-col :span="1">
						<u-icon name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					</u-col>
					<u-col :span="11">
						<u-form>
							<u-form-item :labelWidth="labelWidth" label="工单" borderBottom
								@click="showBarcode=true; hideKeyboard()">
								<u--input v-model="barcode.TShowInfo" disabled disabledColor="#ffffff" placeholder="请选择工单"
									border="none"></u--input>
								<u-icon slot="right" name="arrow-right"></u-icon>
							</u-form-item>
						</u-form>
					</u-col>
				</u-row>
			</view>
			<view v-if="globalInfo.ScanType==3">
				<u-form>
				<u-row>
					<u-col :span="1">
						<u-icon name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					</u-col>
					<u-col :span="11">
							<u-form-item :labelWidth="labelWidth" label="开始裁床号" borderBottom
								@click="showSn=true;snType=1; hideKeyboard()">
								<u--input v-model="startTSn" disabled disabledColor="#ffffff" placeholder="请选择开始裁床号"
									border="none"></u--input>
								<u-icon slot="right" name="arrow-right"></u-icon>
							</u-form-item>
					</u-col>
				</u-row>
				<u-row>
					<u-col :span="1">
					</u-col>
					<u-col :span="11">
						<u-form-item :labelWidth="labelWidth" label="结束裁床号" borderBottom
							@click="showSn=true;snType=2; hideKeyboard()">
							<u--input v-model="endTSn" disabled disabledColor="#ffffff" placeholder="请选择结束裁床号"
								border="none"></u--input>
							<u-icon slot="right" name="arrow-right"></u-icon>
						</u-form-item>
					</u-col>
				</u-row>
				</u-form>
			</view>
			<view v-if="globalInfo.ScanType==5">
				<view class="sv">
					
				</view>
			</view>
			<view v-if="globalInfo.ScanType!=5">
				
				<u-divider v-if="false" text="工单工序"></u-divider>
				<scroll-view scroll-y="true" class="sv" :style="{height:navHeight+'px'}">
					<u-form>
					<u-row>
						<u-col :span="10">
							<span style="color:dodgerblue;">
								{{barcode.TNo || ''}} 
							</span>
							<span style="color:darkcyan;">
								【{{barcode.TName || '无工单'}}】
							</span>
						</u-col>
						<u-col :span="2">
							<div  class="row-l-r">
								<div></div>
								<span style="color:crimson;font-size: 1rem;text-align: right;">
									{{barcode.TQuantity  || ''}}
								</span>
							</div>
						</u-col>
					</u-row>
					<u-row v-if="showMyDept==true" customStyle="margin-bottom: 0px;background-color:white;">
						<u-col span="12">
							<view class="u-demo-block__content">
								<u-tabs :list="deptLists" @change="changeDept"></u-tabs>
							</view>
						</u-col>
					</u-row>
					<u-checkbox-group v-model="checkboxSkus" placement="column" @change="checkboxChange" style="padding-top: 5px;padding-left: 10px;">
						<u-checkbox size="24" labelSize="24" :customStyle="{marginBottom: '10px'}" v-for="(item, index) in skuLists" :key="index"
							v-if="setCheckBoxShow(item)" :label="item.TInfo" :name="item.TName">
						</u-checkbox>
					</u-checkbox-group>		
					<u-row>
						<u-form-item label="数量">
							<u--input type="digit" v-model="quantity" border="none" placeholder="输入完成数量, 默认全部"></u--input>
						</u-form-item>
					</u-row>	
					<u-divider text="默认提交工序"></u-divider>
					<u-row v-if="globalInfo.DefaultSkus.length>0" style="padding: 3px;">
					<!--display: inline-flex;margin-top:10rpx;-->
						<view style="display: inline-flex;margin-top:10rpx;">
							<view v-for="(item, index) in  globalInfo.DefaultSkus" :key="index" style="margin-right: 16rpx;">
								<u-tag :text="item" type="success" plain></u-tag>
							</view>
						</view>
					</u-row>
					</u-form>
				</scroll-view>
			</view>
			
		</view>
		
		<view class="footer">
			<u-row>
				<u-col v-if="globalInfo.ScanType!=2" :span="postBtnSpan" stop>
					<view class="btn-p">
						<u-button @click="doScan" type="primary"> 扫 码 </u-button>
					</view>
				</u-col>
				<u-col v-if="globalInfo.ScanType!=1 && globalInfo.ScanType!=4 && globalInfo.ScanType!=5" :span="postBtnSpan">
					<view class="btn-p">
						<u-button @click="post" type="success" :disabled="isPosting">
							{{globalInfo.DefaultUser.nickname}} 提 交 
						</u-button>
					</view>
				</u-col>
			</u-row>
		</view>
		<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
			:show-confirm-button="true" @cancel="cancelFilter" @confirm="getBarcode()" ref="uModal"
			:async-close="false" cancel-text="重设" confirm-text="搜索">
			<view style="padding-left: 25rpx;padding-right: 25rpx;">
				<u-form ref="uForm">
					<u-form-item :labelWidth="labelWidth" class="u-form-item-area" label="关键词">
						<u-input confirm-type="search" v-model="keyword" @confirm="getBarcode()" />
					</u-form-item>
					<u-form-item :labelWidth="labelWidth" label="裁床号" borderBottom
						@click="showSn=true;snType=0; hideKeyboard()">
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
			:show="showBarcode"
			:columns="[barcodeLists]"
			keyName="TShowInfo"
			@change="changePicker"
			@cancel="cancelPicker"
			@confirm="selectPicker"
		></u-picker>
		<u-picker
			:show="showSn"
			:columns="snLists"
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
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		getAppSku,
		getParameter,
		saveOrder,
		saveAppOrder,
		getBarcode,
		getBarcodeSku,
		postQRCode
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				page:1,
				keyword:'',
				resMsg: {
					errCode: -1,
					msg: '',
					showError: false,
					showSuccess: false,
					eMsg: '',
					sMsg: '',
					msgRemark: ''
				},
				snType:0,
				isPosting:false,
				saveLastPost:true,
				showMyDept:false,
				myDeptName:'',
				myDeptNo:0,
				deptLists:[],
				startTSn:'',
				endTSn:'',
				//selectedBarcode:{},
				labelWidth:90,
				postBtnSpan:12,
				loadStatus: ['loadmore', 'loadmore', 'loadmore', 'loadmore'],
				barcode:{TGuid:'',TNo:'',TName:'',TQuantityB:'',TQuantity:''},
				skuLists: [],
				quantity: '',
				checkboxSkus: [],
				postCheckSku: [],
				uid: '',
				showBarcode: false,
				tabValue: 1,
				nickname: '',
				showProductInfo: false,
				urlLists: [],
				barcodeLists: [],
				scanTypeLists: [],
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
				skuNo:'',
				sn: '',
				style: '',
				snLists: [{
						_id: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],
				pH:0, //窗口高度
				navHeight:0, //元素的所需高度
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			if(uni.$u.mpShare){
				uni.$u.mpShare.title = this.globalInfo.MpShareTitle;
				let sharePath=this.globalInfo.MpSharePath+'?Domain='+this.globalInfo.Domain;
				if(this.hasLogin){
					uni.$u.mpShare.title =this.userInfo.company_name+ this.globalInfo.MpShareTitle;
					sharePath=sharePath+'&TUserId1='+this.userInfo.company_no;
				}
				uni.$u.mpShare.path = sharePath;
			}
			this.urlLists = this.globalInfo.UrlLists
			this.scanTypeLists=this.globalInfo.ScanTypeLists
			// var url = "/" + currentPage.route //当前页面url
			// for (var i = 0; i < this.urlLists.length; i++) {
			// 	if (this.urlLists[i].url == url) {
			// 		this.tabValue = i;
			// 		break;
			// 	}
			// }
			this.getParameter(1)
			
			if(this.globalInfo.ScanType==2 || this.globalInfo.ScanType==3)
				this.getBarcode()
		},
		onShow() {
			this.saveLastPost=this.globalInfo.SaveLastPost
			this.showMyDept=this.globalInfo.ShowMyDept
			//let _this=this;
			//this.$nextTick(()=>{_this.checkboxSkus = _this.globalInfo.DefaultSkus;});
			this.checkboxSkus = this.globalInfo.DefaultSkus;
			if(this.globalInfo.ScanType==1 || this.globalInfo.ScanType==2 || this.globalInfo.ScanType==4 || this.globalInfo.ScanType==5)
				this.postBtnSpan=12
			else
				this.postBtnSpan=6
		},
		onReady() {
			let that=this;
			uni.getSystemInfo({ //调用uni-app接口获取屏幕高度
				success(res) { //成功回调函数
					that._data.pH=res.windowHeight //windoHeight为窗口高度，主要使用的是这个
					let titleH=uni.createSelectorQuery().select(".sv"); //想要获取高度的元素名（class/id）
					titleH.boundingClientRect(data=>{
						let pH=that._data.pH; 
						that._data.navHeight=pH-data.top-50  //计算高度：元素高度=窗口高度-元素距离顶部的距离（data.top）
					}).exec()
				}
			})
		
			// 微信小程序需要用此写法
			//this.$refs.dtP1.setFormatter(this.formatter)
			//this.$refs.dtP2.setFormatter(this.formatter)
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set'
			}),
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
			
			async getParameter(type) {
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					resType: 0,
					pageSize: 200,
					type: type,
					ordBy: 'TSort'
				}
				
				
				let res = await getParameter({params});
				this.deptLists=[{no:0,name:'所有部门'}];
				this.myDeptNo=this.userInfo.dept
				for (let i = 0; i < res.dataLists.length; i++) {
					let item = res.dataLists[i]
					if(this.myDeptNo==item.TSort)
						this.myDeptName=item.TName
					this.deptLists.push({
						no:item.TSort,
						name: item.TName
					})
				}
			},
			changeDept(item) {
				this.myDeptNo = item.index
				this.skuLists=[];
				this.skuLists=this.barcode.TSkuLists;
			},
			setCheckBoxShow(item){
				let canShow=true;
				if(item.isFinish && this.globalInfo.HideFinish)
					canShow=false;
				if(this.myDeptNo>0 && item.TDept!=this.myDeptNo && this.showMyDept)
					canShow=false;
				// if(item.TQuantityB>0){
				// 	let canQty=item.TQuantityB;
				// 	if(item.TNum1>0)
				// 		canQty=canQty*item.TNum1;
				// 	if(item.TFinishQty>0){
				// 			canQty=canQty-item.TFinishQty;
				// 	}
				// 	if(canQty==0 && this.globalInfo.HideFinish)
				// 		canShow=false;
				// }
				return canShow;
			},
			
			checkboxChange(val) {
				this.checkboxSkus = val;
			},
			clearMsg(){
				this.resMsg.msg=''
				this.resMsg.eMsg=''
				this.resMsg.sMsg=''
			},
			doScan() {
				this.clearMsg()
				if(!this.checkPost())
					return;
				this.isPosting=false;
				this.barcode={TGuid:'',TNo:'',TName:'',TQuantityB:'',TQuantity:''};
				this.skuLists=[];
				this.skuNo='';
				let that = this
				uni.scanCode({
					success: function(res) {
						if(that.globalInfo.ScanType!=5){
							that.getSkuList(res.result)
						}
						else{
							that.postScan(res.result);
						}
					}
				});
			},
			//扫码计次 ScanType==5
			async postScan(qrcodeText){
				let params={
					companyNo: this.userInfo.company_no,
					actType:5,
					qrcodeText
				}
				let res=await postQRCode(params);
				if(res.code>=0 && res.code<90){
					this.resMsg.sMsg=res.sMsg;
					this.PlayAudio(0);
				}else{
					this.resMsg.eMsg=res.eMsg;
					this.PlayAudio(2);
				}
			},
			async getSkuList(barcodeText) {
				let that = this;
				this.skuLists=[];
				//默认二维码, 工厂条码快递码
				let barcodeType=0;
				if(this.globalInfo.MyQrcode)
					barcodeType=1;
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					no: barcodeText,
					barcodeType:barcodeType,
					scanType:this.globalInfo.ScanType
				}
				let res = await getAppSku({params});
				this.barcode= res;
				if(this.barcode)
					this.barcode.TShowInfo=this.barcode.TNo+ '【'+this.barcode.TName+ '】';
				console.log(this.barcode)
				//批量裁床号
				if(this.globalInfo.ScanType==3){
					this.startTSn=this.barcode.TSn;
					this.endTSn=this.barcode.TSn;
					return;
				}
				this.skuNo=res.skuNo;
				this.skuLists = this.barcode.TSkuLists;
				//this.updateDefaultCheckSku();
				
				this.checkboxSkus = [];
				
				setTimeout(function() {
					that.isPosting=false;
					//扫工序码
					if(that.globalInfo.ScanType==4){
						if(that.skuLists.length==1){
							that.$nextTick(()=>{
								that.checkboxSkus = [that.skuLists[0].TName];
								that.post();
							});
						}
						return;
					}
					that.$nextTick(()=>{
						//扫码后马上直接提交
						if(that.globalInfo.ScanType==1){
							that.checkboxSkus = that.globalInfo.DefaultSkus;
							that.post();
						}else{
							if(that.postCheckSku.length>0)//已经提交过
								that.checkboxSkus = that.postCheckSku;
							else
								that.checkboxSkus = that.globalInfo.DefaultSkus;
						}
					});
					//that.$nextTick(()=>{that.checkboxSkus = that.postCheckSku;});
					
				}, 500);
				
				
			},
			checkPost(){
				let isOk=true;
				if(!this.hasLogin){
					uni.showToast({
						title:'请先登录.'
					})
					isOk=false;
				}
				if(!this.globalInfo.DefaultUser.uid){
					this.resMsg.eMsg='请先设定员工.'
					uni.showToast({
						title:'请先设定员工.'
					})
					this.navigateTo('/pagesA/ticket/setting');
					isOk=false;
				}
				return isOk;
			},
			//提交 post()
			async post() {
				//this.PlayAudio(3);
				if(this.isPosting)
					return;
				this.clearMsg();
				this.isPosting=true;
				if(!this.checkPost()){
					this.isPosting=false;
					return;
				}
				let skus=[]
				if(this.skuLists==undefined){
					if(this.globalInfo.ScanType==3){
						this.skuLists=this.globalInfo.DefaultSkus;
					}else{
						this.resMsg.msg='错误: 请设定工序.';
						this.isPosting=false;
						return;
					}
				}
				if(this.globalInfo.ScanType==4 && this.skuLists.length==1){
					skus.push(this.skuLists[0].TName)
				}else{
					for(let i=0;i<this.checkboxSkus.length;i++){
						for(let j=0;j<this.skuLists.length;j++){
							if(this.checkboxSkus[i]==this.skuLists[j].TName){
								skus.push(this.checkboxSkus[i])
							}
						}
					}
				}
				//批量裁床号
				if(this.globalInfo.ScanType==3){
					if(this.startTSn==''){
						this.resMsg.msg='错误: 请选择开始裁床号.'
						this.isPosting=false;
						return;
					}
					if(this.endTSn==''){
						this.endTSn=this.startTSn;
					}
					skus=this.globalInfo.DefaultSkus;
				}
				if(skus.length==0){
					this.resMsg.errCode=100;
					this.resMsg.msg='错误: 请设定默认工序'
					this.isPosting=false;
					return
				}
				this.postCheckSku=skus;
				
				//默认二维码, 工厂条码快递码
				let barcodeType=0;
				if(this.globalInfo.MyQrcode)
					barcodeType=1;
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					scanType:this.globalInfo.ScanType,
					barcodeType:barcodeType,
					uid:this.globalInfo.DefaultUser.uid,
					guid:this.barcode.TGuid,
					no:this.barcode.TNo,
					skus:skus,
					skuNo:this.skuNo,
					quantity:this.quantity,
					startTSn:this.startTSn,
					endTSn:this.endTSn
				}
				//post 无{}
				let res = await saveAppOrder(params);
				//console.log(params,res);
				if(res.code>90 || res.eMsg!=''){
					this.PlayAudio(2)
				}else{
					this.PlayAudio(0)
				}
				this.resMsg.msg=res.msg
				this.resMsg.errCode=res.errCode
				this.resMsg.eMsg=res.eMsg
				this.resMsg.sMsg=res.sMsg
				if(res.record)
					this.skuLists=res.record.TSkuLists
				
				if (!this.saveLastPost){
					this.quantity='';
					this.checkboxSkus = [];
					this.isPosting=false;
				}else{
					this.updateDefaultCheckSku();
				}
			},
			updateDefaultCheckSku(){
				let that=this;
				this.checkboxSkus = [];
				setTimeout(function() {
					that.isPosting=false;
					//that.$nextTick(()=>{that.checkboxSkus = that.globalInfo.DefaultSkus;});
					that.$nextTick(()=>{that.checkboxSkus = that.postCheckSku;});
				}, 500);
			},
			async getBarcode(){
				if(!this.checkPost())
					return;
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					pageSize:parseInt(50),
					dt1: this.textDt1.replace(/\//g, "-"),
					dt2: this.textDt2.replace(/\//g, "-"),
					sn: this.sn,
					style: this.style,
					keyword: this.keyword
				}
				uni.showLoading({
					'title':'正在加载数据...'
				});
				let res = await getBarcode({params});
				//console.log('res',res)
				uni.hideLoading();
				this.barcodeLists=[]
				for(let i=0;i<res.dataLists.length;i++){
					let item=res.dataLists[i];
					item.TShowInfo=item.TNo + '【'+item.TName+ '】';
					this.barcodeLists.push(item);
				}
				this.snLists=[]
				this.snLists.push(res.queryInfo.snLists)
				this.styleLists=[]
				this.styleLists.push(res.queryInfo.styleLists)
				this.showFilter=false
			},
			barcodeSelected(e){
				this.barcode= e;
				this.skuLists = this.barcode.TSkuLists;
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
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
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
				let item = e.value[0];
				if(this.showSn){
					if(this.snType==0)
						this.sn=item._id
					if(this.snType==1)
						this.startTSn=item._id
					if(this.snType==2)
						this.endTSn=item._id
				}
				if(this.showStyle)
					this.style=item._id
				if(this.showBarcode){
					this.barcode= item;
					this.getSkuList(item.TNo);
					// this.skuLists = this.barcode.TSkuLists;
					// console.log(this.skuLists);
					// for(let i=0;i<this.skuLists.length;i++){
					// 	this.skuLists[i].TInfo=this.skuLists[i].TName+' ('+this.barcode.TQuantityB+')'
					// }
				}
				this.cancelPicker();
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.cancelPicker()
			},
			cancelPicker() {
				this.showSn = false
				this.showStyle = false
				this.showBarcode = false
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



<style lang="scss" scoped>
 
	.text-green{
		color:green;
	}
	.text-red{
		color:red;
	}
	
</style>
