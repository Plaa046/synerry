<template>
  <div class="short-url-form">
    <h2>สร้าง Short URL</h2>
    <form @submit.prevent="createShortUrl">
      <div>
        <label for="full-url">URL ต้นฉบับ:</label>
        <input type="text" id="full-url" v-model="fullUrl" required />
      </div>
      <button type="submit">สร้าง Short URL</button>
    </form>

    <div v-if="shortUrl">
      <h3>Short URL:</h3>
      <p>{{ shortUrl }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      fullUrl: '',
      shortUrl: ''
    };
  },
  methods: {
    async createShortUrl() {
      try {
        const response = await axios.post('http://localhost:3000/api/shorturls', {
          full_url: this.fullUrl
        });
        this.shortUrl = `https://shorturl.at/${response.data.short_code}`;
      } catch (error) {
        console.error('Error creating short URL:', error);
      }
    }
  }
};
</script>

<style scoped>
.short-url-form {
  max-width: 400px;
  margin: 0 auto;
  text-align: center;
}

input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
}

button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}
</style>
