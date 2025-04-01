<template>
	<div>
		<section class="loginSection">
			<div class="loginBox">
                <form method="post" @submit.prevent="postLogin" @keydown="forms.errors.clear($event.target.name)">
					<div class="row">
						<div class="col-1">
							<div class="placeBoxes">
								<div class="brandArea">
									<!--<img src=" " width="250px" alt="">-->
									<h2 class="brandTitle">MDIT <span>ERP</span></h2>
								</div>
								<div class="inputArea">
									<span><Icon class="loginIcon" type="ios-person-outline"></Icon></span>
                                    <input class="loginInput" type="email" placeholder="E-mail " name="email"
                                           required="required" v-model="forms.email">
								</div>
								<div class="inputArea">
									<span><Icon class="loginIcon" type="key"></Icon></span>
                                    <input class="loginInput" type="password" placeholder="Password " name="password"
                                           required="required" v-model="forms.password">
								</div>
								<button class="loginButton">LOGIN</button>
							</div><br>
							<div class="accessHelp">
                              <span>
                                   <input id="remember" class="rememberCheck
                                   " type="checkbox" name=""  >
                                   <label for="remember" >Remember Me!</label>
                              </span>
								<span>
                                   <a href="">forgotten Password ?</a>
                              </span>
							</div>
						
						</div>
					</div>
				</form>
			</div>
		</section>
	</div>
</template>

<script>
    export default {

        data() {
            return {
                forms: this.$root.$data.forms
            }
        },

        methods: {
            postLogin() {
                axios.post('/login', this.forms).then(response => {
                    let token = response
                    console.log(token);
                }).catch()
            }
        }
    }
</script>