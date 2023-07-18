<template>
	<view>
		<web-view v-if="weburl" :src="weburl"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				weburl:null
			};
		},
		onLoad({url,title}) {
			if(url.substring(0, 4) != 'http'){
				uni.showModal({
					title:"错误",
					content: '不是一个有效的网站链接,'+'"'+url+'"',
					showCancel: false,
					confirmText:"知道了",
					complete: () => {
						uni.navigateBack()
					}
				});
				title = "页面路径错误"
			}else{
				console.log(url,title);
				this.weburl = url;
			}
			if(title){
				uni.setNavigationBarTitle({title});
			}
		}
	}
</script>

<style lang="scss">

</style>
