<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="3" v-for="(icon, key) in $root.icons" :key="key">
        <v-btn>
          {{key}} <v-icon>{{icon}}</v-icon>
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>
<script setup>
</script>