const keyupEvent = e => {
  e.preventDefault();

  if (e.target.id.match('number_')) {
    if (e.target.value.length == 1 && e.target.value[0] !== '+') {
      e.target.value = `+${e.target.value}`;
    } else if (e.target.value.length == 2) {
      e.target.value = '';
    } else if (e.target.value.length == 4 && e.target.value[3] !== ' ') {
      let arr = e.target.value.split('');
      arr[3] = ` (${e.target.value[3]}`;
      e.target.value = arr.join('');
    } else if (e.target.value.length == 5 && e.target.value[4] === '(') {
      e.target.value = e.target.value.slice(0, 3);
    } else if (e.target.value.length == 8) {
      let arr = e.target.value.split('');
      arr[7] = `) ${e.target.value[7]}`;
      e.target.value = arr.join('');
    } else if (e.target.value.length == 9 && e.target.value[8] === ' ') {
      e.target.value = e.target.value.slice(0, -2);
    } else if (e.target.value.length == 15 && e.target.value[14] !== '-') {
      let arr = e.target.value.split('');
      arr[14] = `-${e.target.value[14]}`;
      e.target.value = arr.join('');
    } else if (e.target.value.length == 15 && e.target.value[14] === '-') {
      e.target.value = e.target.value.slice(0, -2);
    }

  }

  if(e.target.className.match('contact_img_button')) {
    if(e.code == 'Enter') {
      e.target.querySelector('input').click();
    }
  }
}

export default keyupEvent;
