<template>
  <div class="player">
    <h2>{{ tracks[currentTrack] }}</h2>
    <img src="" alt="" class="player__img">
    <audio ref="audio" :src="src" :loop="repeatTrack" @timeupdate="timeupdate" @ended="next" @canplay="canplay"></audio>
    <div class="player__timing">
      <span>{{ currentTimeConvert }}</span>
      <span>{{ durationConvert }}</span>
    </div>
    <input ref="progress" type="range" min="0" :max="duration" @input="setProgress">
    <div class="player__nav">
      <button title="Предыдущий трек" @click="prev">⊲⊲</button>
      <button :title="isPlay ? 'Пауза' : 'Воспроизвести'" @click="p">{{ isPlay ? '| |' : '▷' }}</button>
      <button title="Стоп" @click="stop">☐</button>
      <button title="Следующий трек" @click="next">⊳⊳</button>
      <button :repeat="repeatActive" :title="repeatActive" @click="loop">↻</button>
      <input type="range" min="0" v-model="volume" max="100" @input="changeVolume">
      <a :href="src" download title="Скачать трек" class="player__next">🡇</a>
      <button @click="test">Test</button>
    </div>
  </div>
</template>

<script setup>
import moment from 'moment'
import { ref, reactive, watch, computed } from 'vue'

let tracks = reactive([
  '01_storm_inside_nas_bolshe_net_myzuka', 'ApostaZiya - '
])

async function test() {
  let response = await fetch('/test.php')
  if (response.ok) {
    let s = await response.text()
    console.log(s)
  } else {
    alert("Ошибка HTTP: " + response.status)
  }
}

const volume = ref(100)
const duration = ref(0)
const durationConvert = computed(() => {
  if (duration.value > 3599) {
    return moment.utc(duration.value * 1000).format('HH:mm:ss')
  } else {
    return moment.utc(duration.value * 1000).format('mm:ss')    
  }
})
const currentTime = ref(0)
const currentTimeConvert = computed(() => {
  if (duration.value > 3599) {
    return moment.utc(currentTime.value * 1000).format('HH:mm:ss')
  } else {
    return moment.utc(currentTime.value * 1000).format('mm:ss')    
  }
})

const repeatTrack = ref(false)
const repeatList = ref(false)
const repeat = ['Не повторять', 'Повторить трек', 'Повторить плейлист']
const repeatActiveIndex = ref(0)
const repeatActive = computed(() => repeat[repeatActiveIndex.value])

watch(repeatActive, (newValue) => {
  if (newValue === 'Не повторять') {
    repeatTrack.value = false
    repeatList.value = false
  } else if (newValue === 'Повторить трек') {
    repeatTrack.value = true
    repeatList.value = false
  } else {
    repeatTrack.value = false
    repeatList.value = true
  }
})

const loop = () => {
  if(repeatActiveIndex.value < repeat.length - 1) {
    repeatActiveIndex.value++    
  } else {
    repeatActiveIndex.value = 0
  }
}

const isPlay = ref(false)
watch(isPlay, (newValue) => {
  if (newValue) {
    audio.value.play()
  } else {
    audio.value.pause()
  }
})

const currentTrack = ref(0)
const audio = ref()
const src = computed(() => `/music/${tracks[currentTrack.value]}.mp3`)

const p = () => {
  isPlay.value = !isPlay.value
}
const next = () => {
  if (currentTrack.value === tracks.length - 1) {
    currentTrack.value = 0
    if (!repeatList.value) {
      stop()
    }
  } else {
    currentTrack.value++
  }
}
const prev = () => {
  if (currentTrack.value === 0) {
    stop()
  } else {
    currentTrack.value--
  }
}
const stop = () => {
  audio.value.currentTime = 0
  isPlay.value = false
}

const progress = ref()
const timeupdate = () => {
  progress.value.value = audio.value.currentTime
  currentTime.value = audio.value.currentTime
}

const setProgress = () => {
  audio.value.currentTime = progress.value.value
}

const canplay = () => {
  duration.value = audio.value.duration
  if (isPlay.value) {
    audio.value.play()
  } else {
    audio.value.pause()
  }
}

const changeVolume = () => {
  audio.value.volume = Number(volume.value) / 100
}
</script>
