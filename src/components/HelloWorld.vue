<template>
  <div class="short-url-container">
    <div class="short-url-card">
      <h2>✨ สร้าง Short URL ✨</h2>
      <form @submit.prevent="createShortUrl">
        <label for="full-url">🔗 URL ต้นฉบับ:</label>
        <input type="text" id="full-url" v-model="fullUrl" placeholder="ใส่ลิงก์ที่ต้องการย่อ..." required />
        <button type="submit">🎉 สร้าง Short URL</button>
      </form>

      <div v-if="shortUrl" class="result">
        <h3>✅ Short URL:</h3>
        <div class="short-url-box">
          <a :href="shortUrl" target="_blank">{{ shortUrl }}</a>
          <button @click="copyToClipboard">📋 คัดลอก</button>
        </div>
      </div>

      <div v-if="errorMessage" class="error">
        <p>⚠️ {{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      fullUrl: "",
      shortUrl: "",
      errorMessage: "",
    };
  },
  methods: {
    async createShortUrl() {
      this.shortUrl = "";
      this.errorMessage = "";
      
      try {
        const response = await axios.post("http://localhost/synerry/server/shorten-url.php", {
          full_url: this.fullUrl,
        });

        if (response.data.error) {
          this.errorMessage = response.data.error;
        } else {
          this.shortUrl = response.data.short_url;
        }
      } catch (error) {
        this.errorMessage = "เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์";
      }
    },
    copyToClipboard() {
      navigator.clipboard.writeText(this.shortUrl);
      alert("คัดลอก Short URL แล้ว! 📋");
    }
  },
};
</script>

<style scoped>
.short-url-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(135deg, #ffd7e8, #c3d9ff);
}

.short-url-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 400px;
}

h2 {
  font-size: 22px;
  color: #6a5acd;
}

label {
  font-weight: bold;
  color: #444;
}

input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 2px solid #c3d9ff;
  border-radius: 8px;
  outline: none;
}

button {
  background: #ff85a2;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  font-size: 16px;
}

button:hover {
  background: #ff6780;
}

.result {
  margin-top: 20px;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 10px;
}

.short-url-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.short-url-box a {
  color: #007bff;
  text-decoration: none;
}

.short-url-box button {
  background: #6a5acd;
  padding: 5px 10px;
  font-size: 14px;
}

.error {
  color: red;
  margin-top: 10px;
}
</style>
