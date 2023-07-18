<template>
	<view class="container">
		<view class="header">
			<view class="row-l-r">
				<view class="title-text">个人喜好设定</view>
				<view><u-button size="small" type="primary" @click="save()"> 储存 </u-button></view>
			</view>
		</view>
		<view class="body">
			<view class="u-light-color" style="padding: 15rpx;">
				<u-radio-group v-model="scanType" placement="column">
					<u-radio :customStyle="{marginBottom: '8px'}" v-for="(item, index) in scanTypeLists" :key="index"
						:label="item.name" :name="item.value" @change="radioChange">
					</u-radio>
				</u-radio-group>
			</view>
			<view class="u-text-center u-font-18 u-show-box">
				<u-divider text="员工扫码参数设置"></u-divider>
			</view>
			<view class="row-l">
				<view>
					<u-switch color="red" v-model="hideFinish" @change="postHideFinish" />
				</view>
				<view>扫码时隐藏已完成工序</view>
			</view>
			
			<view class="row-l">
				<view>
					<u-switch color="red" v-model="playAudio" @change="postPlayAudio" />
				</view>
				<view>扫码完成结果播放语音提示.</view>
			</view>
			
			<view class="row-l">
				<view>
					<u-switch color="red" v-model="saveLastPost" @change="postSaveLast" />
				</view>
				<view>
					启用保留最后提交工序和数量
				</view>
			</view>
			<view class="row-l">
				<view>
					<u-switch color="red" v-model="showMyDept" @change="postShowMyDept" />
				</view>
				<view>
					仅显示属本员工部门的工序
				</view>
			</view>
			
			<view class="row-l">
				<view>
					<u-switch color="red" v-model="myQrcode" @change="postMyQrcode" />
				</view>
				<view>
					自定义工厂条码/二维码
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
	export default {
		data() {
			return {
				hideFinish: false,
				myQrcode: false,
				showMyDept: false,
				saveLastPost: false,
				playAudio: true,
				scanType: 0,
				radioCheckWidth: 'auto',
				radioCheckWrap: true,
				labelPosition: 'left',
				urlLists: [],
				current: 0,
				showScroll: 0,
				tabLists: [{
						name: '扫码员工'
					},
					{
						name: '选择工序'
					}
				],
				scanTypeLists:[]
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
			//this.radioCheckboxChange(1)
			this.scanTypeLists=this.globalInfo.ScanTypeLists
			this.scanType=this.globalInfo.ScanType
			this.hideFinish=this.globalInfo.HideFinish
			this.playAudio=this.globalInfo.PlayAudio
			this.myQrcode=this.globalInfo.MyQrcode
			this.showMyDept=this.globalInfo.ShowMyDept
			this.saveLastPost=this.globalInfo.SaveLastPost
		},
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
			save(){
				this.setGlobalInfo({name:"ScanType",value:this.scanType})
				this.setGlobalInfo({name:"HideFinish",value:this.hideFinish})
				this.setGlobalInfo({name:"MyQrcode",value:this.myQrcode})
				this.setGlobalInfo({name:"ShowMyDept",value:this.showMyDept})
				this.setGlobalInfo({name:"SaveLastPost",value:this.saveLastPost})
				this.setGlobalInfo({name:"PlayAudio",value:this.playAudio})
				uni.navigateBack();
			},
			postHideFinish(e){
				console.log(e)
				this.hideFinish=e
			},
			postMyQrcode(e){
				this.myQrcode=e
				console.log(e)
			},
			postShowMyDept(e){
				this.showMyDept=e
				console.log(e)
			},
			postSaveLast(e){
				this.saveLastPost=e
				console.log(e)
			},
			postPlayAudio(e){
				this.playAudio=e
				console.log(e)
			},
			groupChange(e) {
				console.log('groupChange', e);
				this.scanType=e
			},
			radioChange(e) {
				console.log('radioChange', e);
				this.scanType=e
			},
			changeTabsVal(item) {
				console.log('item', item);
				this.showScroll = item.index
			}
			

		}
	}
</script>

<style lang="scss">
	.row-1 {
		margin-top: 10rpx;
	}

</style>
