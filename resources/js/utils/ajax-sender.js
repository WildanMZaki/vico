const fetcher = async (url) => {
  let res;
  await $.get(url, async (data, status) => {
    res = (await data);
  });
  return res;
}