<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from "vue";

type test = {
  message: string;
  path: string;
};

const testApi: test = reactive({
  message: "",
  path: "",
});

const show = ref(false);

async function getData() {
  show.value = !show.value;
  try {
    const response = await axios.get("http://127.0.0.1:8070/api");
    testApi.message = response.data.message;
    testApi.path = response.data.path;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
}
</script>

<template>
  <div class="container">
    <button @click="getData">Get Data</button>
    <div v-if="show">{{ testApi.message }}</div>
  </div>
</template>

<style scoped>
.container {
  width: 15rem;
  height: 7rem;
}
</style>