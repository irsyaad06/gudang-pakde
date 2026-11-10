const currency = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const conditions = {
    new: 'Baru',
    like_new: 'Seperti Baru',
    good: 'Bagus',
    fair: 'Cukup',
};

export const formatPrice = (value) => currency.format(Number(value));
export const conditionLabel = (value) => conditions[value] || value;
