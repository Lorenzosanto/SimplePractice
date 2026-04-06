let modoAtual = 1;

function alternarModo() {
  const lista = document.getElementById("sumario");

  lista.classList.remove("modo-botao", "modo-link", "modo-box");

  if (modoAtual === 1) {
    lista.classList.add("modo-link");
    modoAtual = 2;
  } 
  else if (modoAtual === 2) {
    lista.classList.add("modo-box");
    modoAtual = 3;
  } 
  else {
    lista.classList.add("modo-botao");
    modoAtual = 1;
  }
}

function irTela() {
  window.location.href = "Public/pages/tela1.html";
}