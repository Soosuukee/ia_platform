export function formatDate(date: string, includeRelative = false) {
  const currentDate = new Date();

  if (!date.includes("T")) {
    date = `${date}T00:00:00`;
  }

  const targetDate = new Date(date);
  const yearsAgo = currentDate.getFullYear() - targetDate.getFullYear();
  const monthsAgo = currentDate.getMonth() - targetDate.getMonth();
  const daysAgo = currentDate.getDate() - targetDate.getDate();

  let formattedDate = "";

  if (yearsAgo > 0) {
    formattedDate = `il y a ${yearsAgo} an${yearsAgo > 1 ? "s" : ""}`;
  } else if (monthsAgo > 0) {
    formattedDate = `il y a ${monthsAgo} mois`;
  } else if (daysAgo > 0) {
    formattedDate = `il y a ${daysAgo} j`;
  } else {
    formattedDate = "Aujourd’hui";
  }

  const fullDate = targetDate.toLocaleDateString("fr-FR", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  if (!includeRelative) {
    return fullDate;
  }

  return `${fullDate} (${formattedDate})`;
}
