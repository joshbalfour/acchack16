const fs = require('fs')

const getTrees = () => {
	return JSON.parse(fs.readFileSync('./trees.json')).features
		.map(d => d.properties)
		.map(d => {
			return Object.assign(
				{},
				d,
				{lng: d.bbox[0],
					lat: d.bbox[1]}
			)
		}).map(d => ({
			id: d.id,
			lat: d.lat,
			lng: d.lng,
			height: d.height
		}))
}

const saveTrees = () => {
	fs.writeFileSync('trees-sanitised.json', JSON.stringify(getTrees()))
}

console.log('🌳🌳🌳', trees.length)