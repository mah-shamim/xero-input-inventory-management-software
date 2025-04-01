<template>
  <div>
    <div class="container" v-if="isLoaded">
      <div class="row">
        <div class="col-1">
          <h5 class="formTitle">{{ title.text }} UNIT
            <Icon type="android-person-add"></Icon>
          </h5>
          <div class="formStyle">
            <div class="row">
              <div class="col-1">
                <form @submit.prevent="postUnit" @keydown="forms.errors.clear($event.target.name)">
                  <input type="hidden" id="id" name="id" v-model="forms.id"/>
                  <div class="form-element">
                    <label>
                      <strong>Unit Key:</strong>
                    </label>
                    <input type="text" v-model="forms.key" class="input-item"
                           required="required" tabindex="1">
                    <p class="strong danger" v-text="forms.errors.get('key')"></p>
                    <label>
                      <strong>Description</strong>
                    </label>
                    <textarea class="input-item" v-model="forms.description"></textarea>

                    <p class="strong danger" v-text="forms.errors.get('description')"></p>


                    <p class="strong danger" v-text="forms.errors.get('is_primary')"></p>

                  </div>
                  <div class="form-element process"><p></p>
                    <span><b>Primary</b></span>
                    <label class="toggle">
                      <input type="checkbox" name="is_primary" v-model="forms.is_primary">
                      <div class="switch bg-primary">
                      </div>
                    </label>
                  </div>
                  <div class="form-element"><br>
                    <button class="btn dark m" type="submit">{{ button.text }}</button>
                    <button type="button" class="btn warning m" @click="onCancel">
                      {{ button.cancel }}
                    </button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script src="./create.js"></script>
