<template>
	<view class="container">
		<view class="header-bar bg-purple-light">
			<u-row>
				<u-col span="9">
					<view class="demo-layout title-text">
						扫码选工序提交
					</view>
				</u-col>
				<u-col span="3">
					<u-button size="small" type="primary" @click="navigateTo('/pages/ticket/setting')"> 扫码设定 </u-button>
				</u-col>
			</u-row>
		</view>
		
		<view style="padding: 6px;">
			<view class="u-demo-wrap" style="display: inline-flex;margin-top:10rpx;">
				<view class="u-demo-block" v-for="(item, index) in  globalInfo.DefaultSkus" :key="index" style="margin-right: 16rpx;">
					<u-tag :text="item" type="success" plain>
					</u-tag>
				</view>
			</view>
		</view>

		<view style="padding: 6px;">
			<view v-show="showProductInfo==1" class="demo-layout">
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
			<view style="margin-bottom: 10rpx;">
				<view>{{barcode.TNo}}  {{barcode.TName}}</view>
			</view>
			<view style="height: 400rpx;">
				<u-checkbox-group v-model="checkboxSkus" placement="column" @change="checkboxChange">
					<u-checkbox :customStyle="{marginBottom: '8px'}" v-for="(item, index) in skuLists" :key="index"
						:label="setCheckBoxLabel(item)" :name="item.TName">
					</u-checkbox>
				</u-checkbox-group>
			</view>

			<view>
				<u--form>
					<u-form-item label="数量" borderBottom>
						<u--input type="number" v-model="quantity" border="none" placeholder="输入完成数量, 默认全部"></u--input>
					</u-form-item>
				</u--form>
			</view>
			
			<view class="demo-layout" v-show="resMsg.msg!=''">
				{{resMsg.msg}}
			</view>

			<view class="demo-layout text-green" v-show="resMsg.msgSuccess!=''">
				{{resMsg.msgSuccess}}
			</view>
		</view>

		<view class="demo-layout">
			<u-row>
				<u-col :span="6" stop>
					<view class="btn-p">
						<u-button @click="doScan" type="primary">扫 码</u-button>
					</view>
				</u-col>
				<u-col :span="6">
					<view class="btn-p">
						<u-button @click="post" type="success">
							{{globalInfo.DefaultUser.nickname}} 提 交
						</u-button>
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="demo-layout margin-t-40 text-red" v-show="resMsg.msgError!=''">
			{{resMsg.msgError}}
		</view>
		<view class="demo-layout margin-t-40">
			{{resMsg.msgRemark}}
		</view>

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
		saveAppOrder
	} from '@/common/api.js';
	export default {
		data() {
			return {
				page:1,
				canEdit: false,
				resMsg: {
					errCode: -1,
					msg: '',
					showError: false,
					showSuccess: false,
					msgError: '',
					msgSuccess: '',
					msgRemark: ''
				},
				loadStatus: ['loadmore', 'loadmore', 'loadmore', 'loadmore'],
				barcode:{TGuid:'',TNo:'',TName:'',TQuantityB:'',TQuantity:''},
				skuLists: [],
				quantity: '',
				checkboxSkus: [],
				uid: '',
				tabValue: 1,
				nickname: '',
				showProductInfo: false,
				urlLists: [],
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
			this.checkboxSkus = this.globalInfo.DefaultSkus
		},
		onShow() {},
		onReady() {

		},

		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			navigateTo(url) {
				console.log(url)
				//uni.redirectTo({
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				console.log(url)
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
			setCheckBoxLabel(item){
				let label='';
				label=item.TName+'已扫码:'+item.TFinishQty;
				return label;
			},
			checkboxChange(val) {
				this.checkboxSkus = val;
				console.log(this.checkboxSkus)
			},
			clearMsg(){
				this.resMsg.msg=''
				this.resMsg.msgError=''
				this.resMsg.msgSuccess=''
			},
			doScan() {
				let that = this
				this.clearMsg()
				uni.scanCode({
					success: function(res) {
						that.getSkuList(res.result)
					}
				});
			},
			async getSkuList(barcodeText) {
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					no: barcodeText
				}
				let res = await getAppSku({
					params
				});
				console.log(res)
				this.barcode= res.record;
				this.skuLists = res.dataLists;
			},
			async post() {
				this.clearMsg()
				let skus=[]
				for(let i=0;i<this.checkboxSkus.length;i++){
					for(let j=0;j<this.skuLists.length;j++){
						if(this.checkboxSkus[i]==this.skuLists[j].TName){
							//skus.push(this.skuLists[j]._id)
							skus.push(this.checkboxSkus[i])
						}
					}
				}
				if(skus.length==0){
					this.resMsg.errCode=100;
					this.resMsg.msg='请选择工序'
					return
				}
				let params = {
					companyNo: this.userInfo.company_no,
					actType:0,
					uid:this.globalInfo.DefaultUser.uid,
					guid:this.barcode.TGuid,
					skus:skus,
					quantity:this.quantity,
				}
				//post wu {}
				let res = await saveAppOrder(params);
				this.resMsg.msg=res.msg
				this.resMsg.errCode=res.errCode
				this.resMsg.msgError=res.msgError
				this.resMsg.msgSuccess=res.msgSuccess				
			},

		}
	}
</script>



<style lang="scss">
	
	.text-green{
		color:green;
	}
	.text-red{
		color:red;
	}
	
</style>
