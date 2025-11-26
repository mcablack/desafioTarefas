<template>
  <div class="mensagem">
    <h1>📧 Sua Mensagem</h1>
    <p>Olá! Esta é uma mensagem de teste porra 778.</p>
    <button @click="mostrarAlerta">Clique aqui</button>
  </div>
</template>

<script>
export default {
  name: 'MensagemPage',
  methods: {
    mostrarAlerta() {
      alert('Você clicou no botão! 999')
    }
  }
}
</script>

<style scoped>
.mensagem {
  padding: 20px;
}

button {
  background-color: #42b983;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}

button:hover {
  background-color: #35a372;
}
</style>