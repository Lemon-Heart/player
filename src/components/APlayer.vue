<template>
  <div ref="player" class="player">
    <h2><span ref="marquee">{{ tracks[currentTrack] }}</span></h2>
    <audio
      ref="audio"
      :src="src"
      :loop="repeatTrack"
      @timeupdate="timeupdate"
      @ended="next"
      @canplay="canplay"
    ></audio>
    <div class="player__timing"><span>{{ currentTimeConvert }}</span><span>{{ durationConvert }}</span></div>
    <input ref="progress" type="range" min="0" :max="duration" @input="setProgress">
    <div class="player__nav">
      <b-prev  @click="prev"/>
      <b-p :title="isPlay ? 'Пауза' : 'Воспроизвести'" :isPlay="isPlay" @click="p"/>
      <b-next @click="next"/>
      <b-repeat :repeat="repeatActive" :title="repeatActive" @click="loop"/>
      <b-random :random="random" @click="random = !random" />
      <input type="range" min="0" v-model="volume" max="100" class="player__volume" @input="changeVolume">
      <b-download :href="src" />
    </div>
    <div class="player__list">
      <div
        v-for="(track, i) in tracks"
        :key="i"
        :class="currentTrack === i ? 'player__track active' : 'player__track'"
        @click="setTrack(i)"
      >
        {{ track }}
      </div>
    </div>
    <span class="player__pos">{{ currentTrack + 1 }} / {{ tracks.length }}</span>
  </div>
</template>

<script setup>
import moment from 'moment'
import { ref, reactive, watch, computed } from 'vue'
import BDownload from './buttons/b-download.vue'
import BRandom from './buttons/b-random.vue'
import BRepeat from './buttons/b-repeat.vue'
import BNext from './buttons/b-next.vue'
import BPrev from './buttons/b-prev.vue'
import BP from './buttons/b-p.vue'

const player = ref()

// const tracks = reactive([])
// async function getTracks() {
//   let response = await fetch('/test.php')
//   if (response.ok) {
//     let json = await response.json()
//     tracks.push.apply(tracks, json)
//   } else {
//     alert("Ошибка HTTP: " + response.status)
//   }
// }
// getTracks()
const tracks = reactive([
  '01_storm_inside_nas_bolshe_net_myzuka', 'ApostaZiya - ', 'EPOHA_STRANSTVIJ_-_CHelovek_Iz_ZHeleza_(iPleer.fm)', 'ESSE - Геральт', 'Evil Not Alone – Письмо', 'ex-сектор-газа-кладбище-сердец'
])

const marquee = ref()
const marqueeActive = () => {
  if (marquee.value.offsetWidth > player.value.offsetWidth) {
    document.documentElement.style.setProperty('--change', player.value.offsetWidth - marquee.value.offsetWidth + 'px')
  } else {
    document.documentElement.style.setProperty('--change', 0)
  }
}

const random = ref(false)
watch(random, (newValue) => {
  if (newValue) {
    createArrayRandomIndex()
  }
})
const randomException = reactive([])
const createArrayRandomIndex = () => {
  randomException.splice(0, randomException.length);
  for (let i = 0; i < tracks.length; ++i) {
    randomException.push(i)
  }
}
const randomTrack = (max = randomException.length) => {
  const randomNumber = () => Math.floor(Math.random() * max)
  let number = randomException.splice(randomNumber(), 1);
  if (randomException.length === 0) {
    createArrayRandomIndex()
  }
  return number[0]
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
    setTimeout(() => {
      marqueeActive()
    }, 100)
  } else {
    audio.value.pause()
  }
})

const currentTrack = ref(0)
watch(currentTrack, () => {
  setTimeout(() => {
    marqueeActive()
  }, 100)
})
const audio = ref()
const src = computed(() => `/music/${tracks[currentTrack.value]}.mp3`)

const p = () => {
  isPlay.value = !isPlay.value
}

const setTrack = (i) => {
  currentTrack.value = i
}

const next = () => {
  if (currentTrack.value === tracks.length - 1) {
    if (random.value) {
      currentTrack.value = randomTrack()
    } else {
      currentTrack.value = 0
      if (!repeatList.value) {
        stop()
      }
    }
  } else {
    if (random.value) {
      currentTrack.value = randomTrack()
    } else {
      currentTrack.value++
    }
  }
}
const prev = () => {
  if (currentTrack.value === 0) {
    if (random.value) {
      currentTrack.value = randomTrack()
    } else {
      stop()
    }
  } else {
    if (random.value) {
      currentTrack.value = randomTrack()
    } else {
      currentTrack.value--
    }
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
  let percent = (audio.value.currentTime * 100) / audio.value.duration
  progress.value.style.background = `linear-gradient(to right, #734ae8 ${percent}%, #89d4cf ${percent}%)`
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

const changeVolume = (e) => {
  audio.value.volume = Number(volume.value) / 100
  e.target.style.background = `linear-gradient(to right, #734ae8 ${volume.value}%, #89d4cf ${volume.value}%)`
}
</script>
