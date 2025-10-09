export function useFormatters() {
    const formatMoney = (amount) => {
        return new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND' 
        }).format(amount);
    };

    const formatDate = (date) => {
        if (!date) return '';
        return new Date(date).toLocaleDateString('vi-VN');
    };

    const formatDateTime = (datetime) => {
        if (!datetime) return '';
        return new Date(datetime).toLocaleString('vi-VN');
    };

    const formatFileSize = (bytes) => {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return bytes + ' bytes';
        }
    };

    const formatNumber = (number) => {
        return new Intl.NumberFormat('vi-VN').format(number);
    };

    return {
        formatMoney,
        formatDate,
        formatDateTime,
        formatFileSize,
        formatNumber,
    };
}
