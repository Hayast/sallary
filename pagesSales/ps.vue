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
				<u-divider text="品項類別"></u-divider>
			
			<view class="u-demo-block">
				<view>
					<u-grid :border="false" col="3">
						<u-grid-item v-for="(item,listIndex) in menuLists" :key="listIndex" v-if="item.show"
							customStyle="padding-top: 10px; padding-bottom: 10px" @click="clickMenu(item)">
							<u--image :src="item.img" width="48" height="48" mode="widthFix"></u--image>
							<text class="grid-text">{{item.title}}</text>
						</u-grid-item>
					</u-grid>
				</view>
			</view>
			
			<u-divider text="公司"></u-divider>
			
			<view style="text-align: center;padding: 15rpx;" @tap="updateAccount">
				<view>
					<text>{{globalInfo.Company.TCorp || '未設置'}} </text>
				</view>
				<viwe>
					<text @click="callPhone">{{globalInfo.Company.TPhone}} </text>
				</viwe>
				<view style="margin: 20upx;">
					<u-button type="primary" text="分享给朋友" open-type="share"></u-button>
					
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
				urlLists: [],
				showSiteLists: false,
				//Company:{},
				siteLists: [
					['https://n1.yunitapp.com', 'https://i2.yunitapp.com', 'https://n.yunitapp.com']
				],
				listSwiper: [
					'https://i.yunitapp.com/images/03.jpg'
				],
				menuLists: [],
				menuLists1: [{
						id:0,
						name: 'scan',
						color:'#4e4eec',
						title: '品项-A1',
						show:true,
						type:0,
						url:'/pages/index/pl'
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
			//初始化 公司和域名 
			this.setGlobalInfo({name:"TUserId1",value:"300006"})
			this.setGlobalInfo({name:"Domain",value:"https://n3.anxinjijian.cn"})
			console.log(this.globalInfo.TUserId1)
			this.postAstMp("getParameter",6)
			this.postAstMp("getParameter",5)
			this.postAstMp("getCompany",0)
			if(uni.$u.mpShare){
				uni.$u.mpShare.title = this.globalInfo.MpShareTitle;
				let sharePath=this.globalInfo.MpSharePath+'?Domain='+this.globalInfo.Domain;
				if(this.hasLogin){
					uni.$u.mpShare.title =this.userInfo.company_name+ this.globalInfo.MpShareTitle;
					sharePath=sharePath+'&TUserId1='+this.userInfo.company_no;
				}
				console.log(sharePath)
				uni.$u.mpShare.path = sharePath;
			}
		},
		onShow(){
			this.updateAccount();
		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setUserAdmin: 'user/setAdmin',
				setGlobalInfo: 'global/set'
			}),
			async updateAccount(){
				if(!this.hasLogin){
					return;
				}
				let params={
					action:'getCurrentUserInfo',
					uid:this.userInfo.uid
				}
				let res=await astCf(params);
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
			async postAstMp(action,type) {
				let params = {
					action,
					companyNo: this.globalInfo.TUserId1,
					page: 1,
					pageSize: 20,
					type: type
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//注意get的参数  需要({params}) post 不需要
				let res = await astMp(params);
				console.log('postAstMp',params,res)
				uni.hideLoading();
				if(type==6)
					this.listSwiper=[];
				for (let i = 0; i < res.dataLists.length; i++) {
					let item = res.dataLists[i]
					//轮换图片
					if(action=='getParameter'){
						if (type==5){//产品类别
							this.menuLists.push({
								img:this.globalInfo.Domain+"/"+item.TPhoto,
								title: item.TName,
								TSort: item.TSort,
								show:true
							})
							if(this.menuLists.length>0)
								this.setGlobalInfo({name:"ProductType",value:JSON.parse(JSON.stringify(this.menuLists))})
						}
						if (type==6){//轮流图片
							this.listSwiper.push(
								this.globalInfo.Domain+"/"+item.TPhoto
							)
						}
					}
					//产品
					if(action=='getCompany'){
						//this.Company = item
						this.setGlobalInfo({name:"Company",value:item})
					}
					
					//产品
					if(action=='getProduct'){
						this.menuLists.push({
							title: item.TName
						})
					}
					
				}
			},
			clickMenu(item){
				this.setGlobalInfo({name:"CurrProdType",value:item.TSort-1})
				this.switchTab('/pages/index/pl')
				/*
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
				*/
				
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
			changeTabbar(e) {
				this.tabValue = e
				var url = this.urlLists[e].url
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
				//console.log('confirm', e);
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
			},
			//发送给朋友
			onShareAppMessage(res) {
				return {
					title: this.globalInfo.Company.TCorp,
					path: 'pages/index/index',
					imageUrl: this.globalInfo.Domain+'/images/shuangmali.png' //分享图标，路径可以是本地文件路径、代码包文件路径或者网络图片路径.支持PNG及JPG。显示图片长宽比是 5:4
				}
			},
			    //分享到朋友圈
			onShareTimeline(res) {
				return {
					title: this.globalInfo.Company.TCorp,
					query:'pages/index/index',//页面参数
					imageUrl: this.globalInfo.Domain+'/images/shuangmali.png' //分享图标，路径可以是本地文件路径、代码包文件路径或者网络图片路径.支持PNG及JPG。显示图片长宽比是 5:4
				}
			},
			callPhone() {
				wx.makePhoneCall({
					phoneNumber: this.globalInfo.Company.TPhone, //仅为示例，并非真实的电话号码
					success: function() {
						console.log("拨打电话成功！")
					},
					fail: function() {
						console.log("拨打电话失败！")
					}
				})
			},

		}
	}
</script>

<style lang="scss">
	.grid-text{
		font-size: 0.9em;
	}
</style>
