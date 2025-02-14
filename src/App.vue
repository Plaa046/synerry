<template>
  <div id="app">
    <HelloWorld />
  </div>
</template>

<script>
import HelloWorld from './components/HelloWorld.vue';

export default {
  name: 'App',
  components: {
    HelloWorld
  }
}
</script>

<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
  background: linear-gradient(135deg, #ffd7e8, #c3d9ff);  
  height: 100vh;  
  display: flex;
  justify-content: center;
  align-items: center;
}

h1 {
  font-size: 36px;
  color: #6a5acd;
}
</style>
