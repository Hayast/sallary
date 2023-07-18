<template>
	<view class="container">
		<view class="body">
			<u-swiper
			            :list="listSwiper"
						indicator
						indicatorMode="line"
						circular
			            @change="changeSwiper"
			            @click="clickSwiper"
			    ></u-swiper>
				<u-divider text="扫码计件"></u-divider>
			
			<view class="u-demo-block">
				<view>
					<u-grid :border="false" col="4">
						<u-grid-item v-for="(item,listIndex) in menuLists" :key="listIndex" v-if="item.show && item.id<9"
							customStyle="padding-top: 10px; padding-bottom: 10px" @click="clickMenu(item)">
							<u-icon :color="item.color" shape="circle" :customStyle="{paddingTop:10+'rpx'}" :name="item.name" :size="36"></u-icon>
							<text class="grid-text">{{item.title}}</text>
						</u-grid-item>
					</u-grid>
				</view>
			</view>
			<u-divider text="业务库存" v-if="empType<4"></u-divider>
			<view class="u-demo-block">
				<view>
					<u-grid :border="false" col="4">
						<u-grid-item v-for="(item,listIndex) in menuLists" :key="listIndex" v-if="item.show && item.id>8"
							customStyle="padding-top: 10px; padding-bottom: 10px" @click="clickMenu(item)">
							<u-icon :color="item.color" shape="circle" :customStyle="{paddingTop:10+'rpx'}" :name="item.name" :size="36"></u-icon>
							<text class="grid-text">{{item.title}}</text>
						</u-grid-item>
					</u-grid>
				</view>
			</view>
			<u-divider v-if="showSale" text="产品销售"></u-divider>
			<view v-if="showSale" class="u-demo-block">
				<view>
					<u-grid :border="false" col="4">
						<u-grid-item v-for="(item,listIndex) in saleLists" :key="listIndex"
							customStyle="padding-top: 10px; padding-bottom: 10px" @click="clickMenu(item)">
							<u-icon :color="item.color" shape="circle" :customStyle="{paddingTop:10+'rpx'}" :name="item.name" :size="36"></u-icon>
							<text class="grid-text">{{item.title}}</text>
						</u-grid-item>
					</u-grid>
				</view>
			</view>
			<u-divider text="公司"></u-divider>
			
			<view style="text-align: center;padding: 15rpx;" @tap="updateAccount">
				<view>
					<text>{{userInfo.company_name || '未设置'}} </text>
				</view>
				<viwe>
					<text>{{globalInfo.Domain}} </text>
				</viwe>
			</view>
			<view class="floatbtn" @click="changeMenu">
				<view v-if="!menushow">
					<u-icon name="server-fill" size="28" color="red"></u-icon>
				</view>
				<view v-if="menushow">
					<u-icon name="close-circle-fill" size="26" color="#ff5500"></u-icon>
				</view>
			</view>
			<view v-if="menushow" class="menuarea">
				<view class="mask" @click="changeMenu">
				</view>
				<view class="menulist">
					<u-cell-group>
						<u-cell icon="question-circle-fill" title="使用帮助" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com&title=支持')"></u-cell>
						<u-cell v-if="false" icon="chat" title="IOT" @tap="navigateTo('/pagesAst/iot')" arrow-direction="right"></u-cell>
						<u-cell icon="chat" title="在线咨询" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com/chat')" arrow-direction="right"></u-cell>
						<u-cell icon="phone-fill" title="联系我们" @tap="navigateTo('/pages/common/webview/webview?url=https://i.yunitapp.com/contact&title=联系')" arrow-direction="right"></u-cell>
					</u-cell-group>
					
				</view>
			</view>
			
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
		astMp
	} from '@/common/api.js';
	export default {
		
		data() {
			return {
				tabValue: 0,
				showSale: false,
				menushow: false,
				urlLists: [],
				showSiteLists: false,
				siteLists: [
					['https://n1.yunitapp.com', 'https://n1.anxinjijian.cn']
				],
				ptLists:[],//产品分类列表
				listSwiper: [
					'https://i.yunitapp.com/images/01.jpg',
					'https://i.yunitapp.com/images/02.jpg',
					'https://i.yunitapp.com/images/03.jpg'
				],
				
				saleLists:[
					{
							id:0,
							name: 'grid',
							color:'#4e4eec',
							title: '产品列表',
							show:true,
							type:0,
							url:'/pagesSales/pl'
						},
						{
							id:0,
							name: 'shopping-cart',
							color:'#4e4eec',
							title: '购物车',
							show:true,
							type:0,
							url:'/pagesSales/cart'
						},
						{
							id:0,
							name: 'file-text',
							color:'#4e4eec',
							title: '我的订单',
							show:true,
							type:0,
							url:'/pagesSales/myorder'
						}
				],
				
				menuLists: [{
						id:0,
						name: 'scan',
						color:'#4e4eec',
						title: '扫码计数',
						show:true,
						type:1,
						url:'/pagesA/ticket/scan'
					},
					{
						id:0,
						name: 'star',
						color:'#4e4eec',
						title: '个人喜好',
						show:true,
						type:0,
						url:'/pagesA/ticket/mysetting'
					},
					{
						id:0,
						name: 'lock',
						color:'#4e4eec',
						title: '默认设置',
						show:true,
						type:0,
						url:'/pagesA/ticket/setting'
					},
					{
						id:0,
						name: 'rmb',
						color:'#4e4eec',
						title: '个人结算',
						show:true,
						type:0,
						url:'/pagesA/ticket/mysallary'
					},
					{
						id:0,
						name: 'tags',
						color:'#ff5500',
						title: '裁床编菲',
						show:false,
						type:0,
						url:'/pagesA/ticket/create'
					},
					{
						id:1,
						name: 'file-text',
						color:'#ff5500',
						title: '工序模板',
						show:false,
						type:0,
						url:'/pagesA/ticket/ticket'
					},{
						id:2,
						name: 'bookmark',
						color:'#ff5500',
						title: '工菲管理',
						show:false,
						type:0,
						url:'/pagesA/ticket/barcode'
					},{
						id:3,
						name: 'coupon',
						color:'#ff5500',
						title: '计件记录',
						show:false,
						type:0,
						url:'/pagesA/ticket/order'
					},
					{
						id:4,
						name: 'photo',
						color:'#007aff',
						title: '生产进度',
						show:false,
						type:0,
						url:'/pagesA/ticket/process'
					},
					{
						id:5,
						name: 'red-packet',
						color:'#007aff',
						title: '员工工资',
						show:false,
						type:0,
						url:'/pagesA/ticket/mysallary?admin=1'
					},
					{
						id:6,
						name: 'account',
						color:'#007aff',
						title: '员工帐号',
						show:false,
						type:0,
						url:'/pages/common/users'
					},
					{
						id:7,
						name: 'setting',
						color:'#007aff',
						title: '系统参数',
						show:false,
						type:0,
						url:'/pages/common/parameter'
					},
					{
						id:8,
						name: 'attach',
						color:'#00aaff',
						title: '建立工单',
						show:false,
						type:0,
						url:'/pagesA/ticket/create0'
					},
					{
						id:9,
						name: 'integral-fill',
						color:'#00aaff',
						title: '客戶管理',
						show:false,
						type:0,
						url:'/pagesSales/custvend?type=1'
					},
					{
						id:10,
						name: 'server-man',
						color:'#00aaff',
						title: '供应商',
						show:false,
						type:0,
						url:'/pagesSales/custvend?type=2'
					},
					{
						id:11,
						name: 'moments',
						color:'#cccc00',
						title: '产品管理',
						show:false,
						type:0,
						url:'/pagesStore/product'
					},
					{
						id:12,
						name: 'gift-fill',
						color:'#a653f9',
						title: '物料管理',
						show:false,
						type:0,
						url:'/pagesStore/material'
					},
					{
						id:13,
						name: 'red-packet',
						color:'#ff5500',
						title: '业务订单',
						show:false,
						type:0,
						url:'/pagesSales/sales'
					},
					{
						id:14,
						name: 'grid-fill',
						color:'#cccc00',
						title: '产品入库',
						show:false,
						type:0,
						url:'/pagesStore/productio?type=0'
					},
					{
						id:15,
						name: 'grid',
						color:'#cccc00',
						title: '产品出库',
						show:false,
						type:0,
						url:'/pagesStore/productio?type=1'
					},
					{
						id:16,
						name: 'car-fill',
						color:'#a653f9',
						title: '物料入库',
						show:false,
						type:0,
						url:'/pagesStore/materialio?type=0'
					},
					{
						id:17,
						name: 'car',
						color:'#a653f9',
						title: '物料出库',
						show:false,
						type:0,
						url:'/pagesStore/materialio?type=1'
					},
					{
						id:18,
						name: 'coupon',
						color:'#006fa3',
						title: '样品管理',
						show:false,
						type:0,
						url:'/pagesSales/modules'
					},
					{
						id:19,
						name: 'bag',
						color:'#a3f400',
						title: '生产入库',
						show:false,
						type:0,
						url:'/pagesStore/spi'
					},
					{
						id:20,
						name: 'checkmark-circle-fill',
						color:'#00aaff',
						title: '工单检查',
						show:false,
						type:0,
						url:'/pagesA/ticket/qc'
					},
					{
						id:21,
						name: 'email-fill',
						color:'#00aaff',
						title: '快递管理',
						show:false,
						type:0,
						url:'/pagesStore/express'
					},
					{
						id:22,
						name: 'bag',
						color:'#00aaff',
						title: '货架管理',
						show:false,
						type:0,
						url:'/pagesStore/wbio'
					}
				],
				empType:4
			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				admin: 'user/admin',
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
			//this.postAstMp("getParameter",6)
			this.postAstMp("getParameter",5)
			
			// this.urlLists = this.globalInfo.UrlLists
			// var pages = getCurrentPages() //获取加载的页面
			// var currentPage = pages[pages.length - 1] //获取当前页面的对象
			// var url = "/" + currentPage.route //当前页面url
			// for (var i = 0; i < this.urlLists.length; i++) {
			// 	if (this.urlLists[i].url == url) {
			// 		this.tabValue = i;
			// 		break;
			// 	}
			// }
			//this.initApp();
		},
		onShow(){
			this.menushow=false;
			this.updateAccount();
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setUserAdmin: 'user/setAdmin',
				setGlobalInfo: 'global/set'
			}),
			changeMenu() {
                this.menushow = !this.menushow
            },
			async postAstMp(action,type) {
				let params = {
					action,
					companyNo: this.userInfo.company_no,//this.globalInfo.TUserId1,
					page: 1,
					pageSize: 20,
					type: type
				}
				// uni.showLoading({
				// 	'title': '正在加载数据...'
				// });
				//注意get的参数  需要({params}) post 不需要
				//小程序取产品等数据
				let res = await astMp(params);
				//uni.hideLoading();
				if (res!=undefined){
					if(res.dataLists==undefined)
						return;
					if(type==6 && res.dataLists.length>1)
						this.listSwiper=[];
					for (let i = 0; i < res.dataLists.length; i++) {
						let item = res.dataLists[i]
						//轮换图片
						if(action=='getParameter'){
							if (type==5){//产品类别
								this.ptLists.push({
									img:this.globalInfo.Domain+"/"+item.TPhoto,
									title: item.TName,
									TSort: item.TSort,
									show:true
								})
								//产品分类列表
								if(this.ptLists.length>0)
									this.setGlobalInfo({name:"ProductType",value:JSON.parse(JSON.stringify(this.ptLists))})
							}
							if (type==6){//轮流图片
								this.listSwiper.push(
									this.globalInfo.Domain+"/"+item.TPhoto
								)
							}
						}
						//公司
						if(action=='getCompany'){
							this.setGlobalInfo({name:"Company",value:item})
						}					
					
						//产品
						if(action=='getProduct'){
							this.menuLists.push({
								title: item.TName
							})
						}				
					}
				}
							},
			async updateAccount(){
				if(!this.hasLogin){
					return;
				}
				let params={
					companyNo: this.userInfo.company_no,
					action:'getCurrentUserInfo',
					uid:this.userInfo.uid
				}
				let res=await astCf(params);
				if (res==undefined)
					return;
				if(res.code==0){
					if(!res.userInfo || res.userInfo==undefined){
						return
					}
					let info=res.userInfo;
					if(!this.globalInfo.DefaultUser){
						let defaultUser={uid:info._id,nickname:info.nickname}
						this.setGlobalInfo({
							name: "DefaultUser",
							value: defaultUser
						})
					}
					
					let adminInfo={is_delete:info.is_delete,type:info.type,admin:info.admin,username:info.username};
					this.setUserAdmin(adminInfo);
					this.empType=info.type;
					let configCorp=res.company.TConfigCorp||'00000000000000000000000000000000000000'
					if (configCorp.substr(11,1)=="1"){
						this.showSale=true;
					}
					for(let i=0;i<this.menuLists.length-4;i++){
						this.menuLists[i+4].show=false;
					}
					/*
					0扫码计数 1个人喜好 2默认设置3个人结算
					4.裁床编菲 5工序模板 6工单管理 7计件记录 8生产进度 9员工工资10员工帐号11系统参数12建立工单13客戶管理14供应商
					15产品管理16物料管理17业务订单18产品入库19产品出库20物料入库21物料出库22样品管理23生产入库
					0'帐号管理',1'公司资料',2'参数设置',3'批量录入',4'工资管理','5计件记录',6'工单管理',7'工序工价',8'裁床编菲/建立工单',9'生产进度',10'客户管理',//0-10
					11'供应商管理',12'业务订单',13'产品管理',14'产品入库/生产入库',15'产品出库',16'物料管理',17'物料入库',18'物料出库',19'库存调整',20'样品管理'//11-20
					
					*/
					for(let i=0;i<this.menuLists.length-3;i++){
						if(this.admin[i]){
							if(i==0)//帐号{
								this.menuLists[10].show=true;
							//if(i==1)//公司
							//	this.menuLists[i+4].show=true;
							if(i==2)// 參數
								this.menuLists[11].show=true;
							// if(i==3)//批量录入
							// 	this.menuLists[i+4].show=true;
							if(i==4)//工資
								this.menuLists[9].show=true;
							if(i==5)//計件
								this.menuLists[7].show=true;
							if(i==6)//工單
								this.menuLists[6].show=true;
							if(i==7)//工序工價
								this.menuLists[5].show=true;
							if(i==8)//裁床編菲/建立工單
							{
								this.menuLists[4].show=true;
								this.menuLists[12].show=true;
							}
							if(i==9)//生產進度  工单检查
							{
								this.menuLists[8].show=true;	
								this.menuLists[24].show=true;	
							}
							if(i==10)//客户管理
							{
								this.menuLists[13].show=true;	
							}
							if(i==11)//供应商管理
							{
								this.menuLists[14].show=true;	
							}
							if(i==12)//业务订单
							{
								this.menuLists[17].show=true;	
							}
							if(i==13)//产品管理
							{
								this.menuLists[15].show=true;	
								this.menuLists[25].show=true;	//快递
								this.menuLists[26].show=true;	//货架
							}
							if(i==14)//产品入库/生产入库
							{
								this.menuLists[18].show=true;	
								this.menuLists[23].show=true;	
							}
							if(i==15)//产品出库
							{
								this.menuLists[19].show=true;	
							}
							if(i==16)//物料管理
							{
								this.menuLists[16].show=true;	
							}
							if(i==17)//物料入库
							{
								this.menuLists[20].show=true;	
							}
							if(i==18)//物料出库
							{
								this.menuLists[21].show=true;	
							}
							if(i==19)//
							{
								//this.menuLists[22].show=true;	
							}
							if(i==20)//样品管理
							{
								this.menuLists[22].show=true;	
							}
								
						}
					}
				}
				
			},
			async initApp() {
				//
			},
			getAdminShow(idx){
				if(idx<4)
					return true;
				if(idx>3 && this.admin[idx-4])
					return true;
			},
			setGlobal(val) {
				this.setGlobalInfo({
					name: 'Domain',
					value: val
				})
			},
			clickMenu(item){
				if(item.type==0)
					this.navigateTo(item.url)
				if(item.type==1){
					if(item.name=='scan'){
						if(this.globalInfo.ScanType==5){
							this.navigateTo('/pagesA/scancount/scancount')
							//this.navigateTo('/pagesA/scancount/scan')
						}
						else
							this.switchTab('/pages/index/scan')
					}
					
				}
			},
			navigateTo(url) {
				this.menushow=false;
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				this.menushow=false;
				uni.switchTab({
					url
				})
			},
			changeTabbar(e) {
				this.tabValue = e
				var url = this.urlLists[e].url
				console.log(url)
				uni.redirectTo({
					url
				})
			},
			changeSwiper(e){
				//console.log(e)
			},
			clickSwiper(e){
				console.log(e)
			},
			
			confirmSite(e) {
				this.setGlobalInfo({
					name: 'Domain',
					value: e.value[0]
				})
				this.showSiteLists = false
				// const updateManager = wx.getUpdateManager()
				// updateManager.applyUpdate()
				uni.reLaunch({
					url:`/pages/index/index`
				})
			},
			cancelSite() {
				this.showSiteLists = false
			}

		}
	}
</script>
<style>
    .floatbtn {
        background-color: #00aaff;
        color: #fff;
        width: 75rpx;
        height: 60rpx;
        position: fixed;
        right: 0;
        bottom: 0;
        z-index: 99999;
        border-radius: 100rpx 0rpx 0 0rpx;
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: flex-end;
        padding-top: 15rpx;
    }
 
    .menuarea {
        width: 100%;
        height: 100%;
    }
 
    .mask {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 88888;
        background-color: #f7f7f7;
    }
 
    .menulist {
        position: fixed;
        right: 0;
        bottom: 86rpx;
        width: 40vw;
        height: 300rpx;
        z-index: 99999;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }
 
 
 
</style>
<style lang="scss">
</style>
